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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Automation\Task\Status as TaskStatus;
use Aheadworks\Helpdesk2\Model\Automation\Action\Pool as ActionPool;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Class Runner
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Task
 */
class Runner
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var Loader
     */
    private $taskLoader;

    /**
     * @var TaskResourceModel
     */
    private $taskResourceModel;

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var EventDataInterfaceFactory
     */
    private $eventDataFactory;

    /**
     * @param JsonSerializer $jsonSerializer
     * @param Loader $taskLoader
     * @param TaskResourceModel $taskResourceModel
     * @param ActionPool $actionPool
     * @param TicketRepositoryInterface $ticketRepository
     * @param EventDataInterfaceFactory $eventDataFactory
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        Loader $taskLoader,
        TaskResourceModel $taskResourceModel,
        ActionPool $actionPool,
        TicketRepositoryInterface $ticketRepository,
        EventDataInterfaceFactory $eventDataFactory
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->taskLoader = $taskLoader;
        $this->taskResourceModel = $taskResourceModel;
        $this->actionPool = $actionPool;
        $this->ticketRepository = $ticketRepository;
        $this->eventDataFactory = $eventDataFactory;
    }

    /**
     * Run scheduled tasks
     *
     * @return bool
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function run()
    {
        $pendingTasks = $this->taskLoader->getPendingTasks();
        foreach ($pendingTasks as $task) {
            $actionHandler = $this->actionPool->getActionHandler($task->getActionType());
            $actionHandler->run(
                $this->jsonSerializer->unserialize($task->getActionData()),
                $this->getEventData($task)
            );

            $task->setStatus(TaskStatus::RUNNING);
            $this->taskResourceModel->save($task);
        }

        return true;
    }

    /**
     * Get event data
     *
     * @param TaskInterface $task
     * @return EventDataInterface
     * @throws NoSuchEntityException
     */
    private function getEventData($task)
    {
        /** @var EventDataInterface $eventData */
        $eventData = $this->eventDataFactory->create();
        $ticket = $this->ticketRepository->getById($task->getTicketId());
        $eventData
            ->setTicket($ticket)
            ->setEventName(Event::RECURRING_TASK);

        return $eventData;
    }
}
