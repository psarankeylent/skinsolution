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
namespace Aheadworks\Helpdesk2\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResource;
use Aheadworks\Helpdesk2\Model\Ticket\Detector;

/**
 * Class TicketService
 *
 * @package Aheadworks\Helpdesk2\Model\Service
 */
class TicketService implements TicketManagementInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var TicketResource
     */
    private $ticketResource;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param MessageRepositoryInterface $messageRepository
     * @param TicketResource $ticketResource
     * @param Detector $detector
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        MessageRepositoryInterface $messageRepository,
        TicketResource $ticketResource,
        Detector $detector
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->messageRepository = $messageRepository;
        $this->ticketResource = $ticketResource;
        $this->detector = $detector;
    }

    /**
     * @inheritdoc
     */
    public function createNewTicket($ticket, $message)
    {
        try {
            $this->ticketResource->beginTransaction();

            $this->ticketRepository->save($ticket);
            $message->setTicketId($ticket->getEntityId());
            $this->messageRepository->save($message);

            $dataToDetect = [
                'ticket' => $ticket,
                'message' => $message
            ];
            $this->detector->detect($dataToDetect, Detector::NEW_TICKET_TYPE);
            $this->ticketResource->commit();
        } catch (\Exception $e) {
            $this->ticketResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateTicket($ticket)
    {
        try {
            $this->ticketResource->beginTransaction();

            $oldTicket = $this->ticketRepository->getById($ticket->getEntityId(), true);
            $this->ticketRepository->save($ticket);
            $dataToDetect = [
                'new_ticket' => $ticket,
                'old_ticket' => $oldTicket
            ];
            $this->detector->detect($dataToDetect, Detector::TICKET_UPDATED_TYPE);
            $this->ticketResource->commit();
        } catch (\Exception $e) {
            $this->ticketResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function createNewMessage($message)
    {
        try {
            $this->ticketResource->beginTransaction();

            $this->messageRepository->save($message);
            $dataToDetect = [
                'message' => $message,
                'ticket' => $this->ticketRepository->getById($message->getTicketId())
            ];
            $this->detector->detect($dataToDetect, Detector::NEW_MESSAGE_TYPE);
            $this->ticketResource->commit();
        } catch (\Exception $e) {
            $this->ticketResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function escalateTicket($ticketId, $escalationMessage)
    {
        try {
            $this->ticketResource->beginTransaction();

            $ticket = $this->ticketRepository->getById($ticketId);
            $dataToDetect = [
                'ticket' => $ticket,
                'escalation-message' => $escalationMessage
            ];
            $this->detector->detect($dataToDetect, Detector::TICKET_ESCALATED_TYPE);
            $this->ticketResource->commit();
        } catch (\Exception $e) {
            $this->ticketResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return true;
    }
}
