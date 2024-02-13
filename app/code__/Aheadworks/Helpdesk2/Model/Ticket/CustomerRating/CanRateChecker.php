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
namespace Aheadworks\Helpdesk2\Model\Ticket\CustomerRating;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CanRateChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\CustomerRating
 */
class CanRateChecker
{
    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var int
     */
    private $duringDays;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param DateTime $dateTime
     * @param int $duringDays
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory,
        DateTime $dateTime,
        $duringDays = 15
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->dateTime = $dateTime;
        $this->duringDays = $duringDays;
    }

    /**
     * Check if customer can rate the ticket
     *
     * @param TicketInterface $ticket
     * @return bool
     */
    public function canRate($ticket)
    {
        $lastMessage = $this->getLastMessage($ticket->getEntityId());
        $lastMessageTimeStamp = $this->dateTime->timestamp($lastMessage->getCreatedAt());
        $currentTimestamp = $this->dateTime->timestamp();

        return ($currentTimestamp - $lastMessageTimeStamp) <= $this->duringDays * 86400;
    }

    /**
     * Retrieve ticket last message
     *
     * @param int $ticketId
     * @return \Magento\Framework\DataObject|MessageInterface
     */
    private function getLastMessage($ticketId)
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($ticketId);
        $messageCollection->addDiscussionTypeFilter();
        $messageCollection->sortByCreatedAt(SortOrder::SORT_DESC);

        return $messageCollection->getFirstItem();
    }
}
