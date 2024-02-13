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

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Pool as ActionPool;

/**
 * Class TicketEscalatedHandler
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Event
 */
class TicketEscalatedHandler
{
    const TICKET_ESCALATED_ACTION = 'send_email_to_supervisor';

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @param ActionPool $actionPool
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        ActionPool $actionPool,
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory
    ) {
        $this->actionPool = $actionPool;
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
    }

    /**
     * Trigger automation event handler
     *
     * @param EventDataInterface $eventData
     * @return $this
     * @throws LocalizedException
     */
    public function trigger(EventDataInterface $eventData)
    {
        $actionData['action'] = self::TICKET_ESCALATED_ACTION;
        $actionHandler = $this->actionPool->getActionHandler($actionData['action']);
        $result = $actionHandler->run($actionData, $eventData);
        if ($result) {
            $this->createMessage($eventData->getTicket());
        } else {
            throw new LocalizedException(__('Ticket cannot be escalated'));
        }

        return $this;
    }

    /**
     * Create escalation system message
     *
     * @param TicketInterface $ticket
     * @return MessageInterface
     * @throws CouldNotSaveException
     */
    private function createMessage($ticket)
    {
        $message = $this->messageFactory->createEscalation();
        $message
            ->setTicketId($ticket->getEntityId())
            ->setAuthorName($ticket->getCustomerName())
            ->setAuthorEmail($ticket->getCustomerEmail());

        return $this->messageRepository->save($message);
    }
}
