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
namespace Aheadworks\Helpdesk2\Model\Automation\Event;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\ConditionMatcher;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Automation\Loader as AutomationLoader;
use Aheadworks\Helpdesk2\Model\Automation\Task\Scheduler as TaskScheduler;

/**
 * Class RecurringTaskHandler
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Event
 */
class RecurringTaskHandler
{
    /**
     * @var AutomationLoader
     */
    private $automationLoader;

    /**
     * @var ConditionMatcher
     */
    private $conditionMatcher;

    /**
     * @var TaskScheduler
     */
    private $taskScheduler;

    /**
     * @param AutomationLoader $automationLoader
     * @param ConditionMatcher $conditionMatcher
     * @param TaskScheduler $taskScheduler
     */
    public function __construct(
        AutomationLoader $automationLoader,
        ConditionMatcher $conditionMatcher,
        TaskScheduler $taskScheduler
    ) {
        $this->automationLoader = $automationLoader;
        $this->conditionMatcher = $conditionMatcher;
        $this->taskScheduler = $taskScheduler;
    }

    /**
     * Trigger automation recurring task
     *
     * @return $this
     * @throws LocalizedException
     */
    public function trigger()
    {
        $automationList = $this->automationLoader->loadByEventName(Event::RECURRING_TASK);
        $processedTicketIds = [];
        foreach ($automationList as $automation) {
            /** @var array $actions */
            $actions = $automation->getActions();
            $ticketIds = $this->conditionMatcher->getMatchedTicketIds($automation);
            if (empty($ticketIds)) {
                continue;
            }

            foreach ($ticketIds as $ticketId) {
                if (in_array($ticketId, $processedTicketIds)) {
                    continue;
                }
                foreach ($actions as $actionData) {
                    if (!isset($actionData['action']) || !isset($actionData['value'])) {
                        throw new LocalizedException(__('Automation action data is not correct'));
                    }
                    $this->taskScheduler->schedule($automation, $actionData, $ticketId);
                }

                if ($automation->getIsRequiredToBreak()) {
                    $processedTicketIds[] = $ticketId;
                }
            }
        }

        return $this;
    }
}
