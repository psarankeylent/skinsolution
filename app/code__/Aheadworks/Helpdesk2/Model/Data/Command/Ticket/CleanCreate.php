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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CleanCreate
 *
 * Class is used to create ticket preventing triggering automations
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class CleanCreate implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var TicketInterfaceFactory
     */
    private $ticketFactory;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketRepositoryInterface $ticketRepository
     * @param MessageRepositoryInterface $messageRepository
     * @param TicketInterfaceFactory $ticketFactory
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TicketRepositoryInterface $ticketRepository,
        MessageRepositoryInterface $messageRepository,
        TicketInterfaceFactory $ticketFactory,
        MessageInterfaceFactory $messageFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketRepository = $ticketRepository;
        $this->messageRepository = $messageRepository;
        $this->ticketFactory = $ticketFactory;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($ticketData)
    {
        $ticket = $this->prepareTicket($ticketData);
        $message = $this->prepareMessage($ticketData);
        $this->ticketRepository->save($ticket);
        $message->setTicketId($ticket->getEntityId());
        $this->messageRepository->save($message);

        return $ticket;
    }

    /**
     * Prepare ticket
     *
     * @param array $data
     * @return TicketInterface
     */
    private function prepareTicket($data)
    {
        /** @var TicketInterface $ticket */
        $ticket = $this->ticketFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticket,
            $data,
            TicketInterface::class
        );

        return $ticket;
    }

    /**
     * Prepare ticket
     *
     * @param array $data
     * @return MessageInterface
     */
    private function prepareMessage($data)
    {
        /** @var MessageInterface $message */
        $message = $this->messageFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $message,
            $data,
            MessageInterface::class
        );

        return $message;
    }
}
