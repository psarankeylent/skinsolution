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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message;

use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message as MessageModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(MessageModel::class, MessageResourceModel::class);
    }

    /**
     * Add ticket filter
     *
     * @param int $ticketId
     * @return $this
     */
    public function addTicketFilter($ticketId)
    {
        $this->addFieldToFilter(MessageInterface::TICKET_ID, $ticketId);
        return $this;
    }

    /**
     * Add discussion type filter
     *
     * @return $this
     */
    public function addDiscussionTypeFilter()
    {
        return $this->addFieldToFilter(
            MessageInterface::TYPE,
            [
                'in' => [
                    MessageType::CUSTOMER,
                    MessageType::ADMIN,
                    MessageType::ESCALATION
                ]
            ]
        );
    }

    /**
     * Add message type filter
     *
     * @param string $type
     * @return $this
     */
    public function addMessageTypeFilter($type)
    {
        $this->addFieldToFilter(MessageInterface::TYPE, $type);
        return $this;
    }

    /**
     * Sort by created at desc
     *
     * @param string $direction
     * @return $this
     */
    public function sortByCreatedAt($direction = SortOrder::SORT_DESC)
    {
        return $this->addOrder(MessageInterface::CREATED_AT, $direction);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            MessageResourceModel::ATTACHMENT_TABLE_NAME,
            MessageInterface::ID,
            MessageAttachmentInterface::MESSAGE_ID,
            [
                MessageAttachmentInterface::ID,
                MessageAttachmentInterface::MESSAGE_ID,
                MessageAttachmentInterface::FILE_PATH,
                MessageAttachmentInterface::FILE_NAME,
            ],
            MessageInterface::ATTACHMENTS,
            [],
            [],
            true
        );

        return parent::_afterLoad();
    }
}
