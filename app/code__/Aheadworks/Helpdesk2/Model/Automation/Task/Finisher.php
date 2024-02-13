<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\Automation\Task;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\ConditionMatcher;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action;
use Aheadworks\Helpdesk2\Model\Automation\Loader as AutomationLoader;

/**
 * Class Finisher
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Task
 */
class Finisher
{
    /**
     * @var Loader
     */
    private $taskLoader;

    /**
     * @var TaskResourceModel
     */
    private $taskResourceModel;

    /**
     * @var AutomationLoader
     */
    private $automationLoader;

    /**
     * @var ConditionMatcher
     */
    private $conditionMatcher;

    /**
     * @param Loader $taskLoader
     * @param TaskResourceModel $taskResourceModel
     * @param AutomationLoader $automationLoader
     * @param ConditionMatcher $conditionMatcher
     */
    public function __construct(
        Loader $taskLoader,
        TaskResourceModel $taskResourceModel,
        AutomationLoader $automationLoader,
        ConditionMatcher $conditionMatcher
    ) {
        $this->taskLoader = $taskLoader;
        $this->taskResourceModel = $taskResourceModel;
        $this->automationLoader = $automationLoader;
        $this->conditionMatcher = $conditionMatcher;
    }

    /**
     * Finish processing tasks
     *
     * @return bool
     * @throws \Exception
     */
    public function finish()
    {
        $runningTasks = $this->taskLoader->getRunningTasks();
        foreach ($runningTasks as $task) {
            if ($this->isTaskStillActual($task)) {
                continue;
            }
            $this->taskResourceModel->delete($task);
        }

        return true;
    }

    /**
     * Check if task can still affect ticket
     *
     * Validate ticket and don't allow to send duplicated emails
     *
     * @param TaskInterface $task
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isTaskStillActual($task)
    {
        if (!in_array($task->getActionType(), [Action::SEND_EMAIL_TO_AGENT, Action::SEND_EMAIL_TO_CUSTOMER])) {
            return false;
        }

        $automation = $this->automationLoader->loadById($task->getAutomationId());
        return $this->conditionMatcher->isTicketMatched($automation, $task->getTicketId());
    }
}
