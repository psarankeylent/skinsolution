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

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task\Collection as TaskCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task\CollectionFactory as TaskCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Automation\Task\Status as TaskStatus;

/**
 * Class Scheduler
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Task
 */
class Scheduler
{
    /**
     * @var TaskCollectionFactory
     */
    private $taskCollectionFactory;

    /**
     * @var TaskInterfaceFactory
     */
    private $taskFactory;

    /**
     * @var TaskResourceModel
     */
    private $taskResourceModel;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param TaskCollectionFactory $taskCollectionFactory
     * @param TaskInterfaceFactory $taskFactory
     * @param TaskResourceModel $taskResourceModel
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        TaskCollectionFactory $taskCollectionFactory,
        TaskInterfaceFactory $taskFactory,
        TaskResourceModel $taskResourceModel,
        JsonSerializer $jsonSerializer
    ) {
        $this->taskCollectionFactory = $taskCollectionFactory;
        $this->taskFactory = $taskFactory;
        $this->taskResourceModel = $taskResourceModel;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Schedule new automation task
     *
     * @param AutomationInterface $automation
     * @param array $actionData
     * @param int $ticketId
     * @return bool
     * @throws AlreadyExistsException
     */
    public function schedule($automation, $actionData, $ticketId)
    {
        if (!$this->isActionAlreadyScheduled($actionData['action'], $ticketId)) {
            /** @var TaskInterface $task */
            $task = $this->taskFactory->create();
            $task
                ->setActionType($actionData['action'])
                ->setActionData($this->jsonSerializer->serialize($actionData))
                ->setAutomationId($automation->getId())
                ->setTicketId($ticketId)
                ->setStatus(TaskStatus::PENDING);

            $this->taskResourceModel->save($task);
            return true;
        }

        return false;
    }

    /**
     * Is action is already applied
     *
     * @param string $actionType
     * @param int $ticketId
     * @return bool
     */
    private function isActionAlreadyScheduled($actionType, $ticketId)
    {
        /** @var TaskCollection $taskCollection */
        $taskCollection = $this->taskCollectionFactory->create();
        $taskCollection
            ->addTicketFilter($ticketId)
            ->addActionTypeFilter($actionType)
            ->addStatusUndoneFilter();

        return $taskCollection->getSize() !== 0;
    }
}
