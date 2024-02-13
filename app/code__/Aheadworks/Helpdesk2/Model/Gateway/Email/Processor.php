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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\RejectedMessageRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor\EmailParser;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor\MessageFactory;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor\TicketFactory;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\Type\Email;
use Aheadworks\Helpdesk2\Model\Rejection\Validator;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Email\Status as EmailStatus;
use Aheadworks\Helpdesk2\Model\Source\RejectedMessage\Type as RejectedMessageType;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Processor
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class Processor
{
    /**
     * @var Validator
     */
    private $rejectionValidator;

    /**
     * @var TicketFactory
     */
    private $ticketFactory;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var EmailParser
     */
    private $emailParser;

    /**
     * @var RejectedMessageInterfaceFactory
     */
    private $rejectedMessageFactory;

    /**
     * @var RejectedMessageRepositoryInterface
     */
    private $rejectedMessageRepository;

    /**
     * @param Validator $rejectionValidator
     * @param TicketFactory $ticketFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param TicketManagementInterface $ticketManagement
     * @param MessageFactory $messageFactory
     * @param EmailParser $emailParser
     * @param RejectedMessageInterfaceFactory $rejectedMessageFactory
     * @param RejectedMessageRepositoryInterface $rejectedMessageRepository
     */
    public function __construct(
        Validator $rejectionValidator,
        TicketFactory $ticketFactory,
        TicketRepositoryInterface $ticketRepository,
        TicketManagementInterface $ticketManagement,
        MessageFactory $messageFactory,
        EmailParser $emailParser,
        RejectedMessageInterfaceFactory $rejectedMessageFactory,
        RejectedMessageRepositoryInterface $rejectedMessageRepository
    ) {
        $this->rejectionValidator = $rejectionValidator;
        $this->ticketFactory = $ticketFactory;
        $this->ticketRepository = $ticketRepository;
        $this->ticketManagement = $ticketManagement;
        $this->messageFactory = $messageFactory;
        $this->emailParser = $emailParser;
        $this->rejectedMessageFactory = $rejectedMessageFactory;
        $this->rejectedMessageRepository = $rejectedMessageRepository;
    }

    /**
     * Process email
     *
     * @param EmailInterface $mail
     * @return EmailInterface
     * @throws LocalizedException
     */
    public function process($mail)
    {
        $validationResult = $this->rejectionValidator->validate($mail);
        if ($validationResult->isRejected()) {
            return $this->rejectMessage($mail, $validationResult->getPatternId());
        }

        return $this->parseMail($mail);
    }

    /**
     * Parse email
     *
     * @param EmailInterface $mail
     * @return EmailInterface
     * @throws LocalizedException
     */
    public function parseMail($mail)
    {
        try {
            $ticketUID = $this->parseUid($mail->getSubject());
            $ticket = $this->ticketRepository->getByUid($ticketUID);
        } catch (NoSuchEntityException $e) {
            $ticket = null;
        }

        if ($ticket) {
            $ticket->setStatusId(TicketStatus::OPEN);
        } else {
            $ticket = $this->ticketFactory->create($mail);
            $ticket->setSubject($mail->getSubject());
        }

        if ($mail->getCcRecipients()) {
            $mailCcList = explode(',', $mail->getCcRecipients());
            $resultCc = [];
            foreach ($mailCcList as $cc) {
                $resultCc[] = $this->emailParser->parseFromSubject($cc);
            }
            $ticket->setCcRecipients(implode(', ', $resultCc));
        }

        $message = $this->messageFactory->create($ticket, $mail);
        if (!$ticket->getEntityId()) {
            $this->ticketManagement->createNewTicket($ticket, $message);
        } else {
            $this->ticketManagement->updateTicket($ticket);
            $message->setTicketId($ticket->getEntityId());
            $this->ticketManagement->createNewMessage($message);
        }

        $mail->setTicketMessageId($message->getId());
        $mail->setStatus(EmailStatus::PROCESSED);

        return $mail;
    }

    /**
     * Parse UID from subject
     *
     * @param string $subject
     * @return null|string
     */
    private function parseUid($subject)
    {
        $ticketUID = null;
        if (preg_match("/\[#([a-zA-Z]{3}-[0-9]{5})\]/i", $subject, $matches)) {
            if (isset($matches[1])) {
                $ticketUID = strtoupper($matches[1]);
            }
        }

        return $ticketUID;
    }

    /**
     * Reject message
     *
     * @param EmailInterface $email
     * @param int $rejectionPatternId
     * @return EmailInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function rejectMessage($email, $rejectionPatternId)
    {
        $email->setRejectPatternId($rejectionPatternId);
        $email->setStatus(EmailStatus::REJECTED);

        /** @var RejectedMessageInterface $rejectedMessage */
        $rejectedMessage = $this->rejectedMessageFactory->create();
        $rejectedMessage
            ->setType(RejectedMessageType::EMAIL)
            ->setFrom($email->getFrom())
            ->setSubject($email->getSubject())
            ->setContent($email->getBody())
            ->setRejectPatternId($rejectionPatternId)
            ->setMessageData([Email::GATEWAY_EMAIL_ID => $email->getId()]);
        $this->rejectedMessageRepository->save($rejectedMessage);

        return $email;
    }
}
