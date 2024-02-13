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
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Phrase;

/**
 * Class TicketLocking
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated
 */
class TicketLocking implements DetectorInterface
{
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function detect($data)
    {
        /** @var TicketInterface $oldTicket */
        $oldTicket = $data['old_ticket'];
        /** @var TicketInterface $newTicket */
        $newTicket = $data['new_ticket'];

        if ($newTicket->getIsLocked() && !$oldTicket->getIsLocked()) {
            $this->createMessage($newTicket->getEntityId(), __('Ticket has been locked'));
        }
        if (!$newTicket->getIsLocked() && $oldTicket->getIsLocked()) {
            $this->createMessage($newTicket->getEntityId(), __('Ticket has been unlocked'));
        }
    }

    /**
     * Create ticket locking message
     *
     * @param int $ticketId
     * @param Phrase $phrase
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createMessage($ticketId, $phrase)
    {
        $message = $this->messageFactory->createTicketLock();
        $message
            ->setTicketId($ticketId)
            ->setContent($phrase);

        $this->messageRepository->save($message);
    }
}
