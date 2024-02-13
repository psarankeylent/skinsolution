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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status;

use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class NewStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status
 */
class NewStatus extends DefaultStatus
{
    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        if ($this->isTicketCreatedByCustomer()) {
            return 100;
        }
        return parent::getPoints();
    }

    /**
     * Get admin message count
     *
     * @return int
     */
    private function isTicketCreatedByCustomer()
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($this->ticket->getEntityId());
        $messageCollection->addDiscussionTypeFilter();
        $messageCollection->setOrder(MessageInterface::CREATED_AT, SortOrder::SORT_ASC);
        /** @var MessageInterface $firstMessage */
        $firstMessage = $messageCollection->getFirstItem();
        return $firstMessage->getType() == MessageType::CUSTOMER;
    }
}
