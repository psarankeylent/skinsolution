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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class Message
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class Message extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_message';
    const ATTACHMENT_TABLE_NAME = 'aw_helpdesk2_ticket_message_attachment';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, MessageInterface::ID);
    }

    /**
     * Get ticket ID by attachment ID
     *
     * @param int $attachmentId
     * @return int|bool
     * @throws LocalizedException
     */
    public function getTicketIdByAttachmentId($attachmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['main_table' => $this->getMainTable()], MessageInterface::TICKET_ID)
            ->joinLeft(
                ['attach_tbl' => $this->getTable(self::ATTACHMENT_TABLE_NAME)],
                'main_table.id = attach_tbl.message_id',
                []
            )
            ->where('attach_tbl.id = ?', $attachmentId);

        $result = $connection->fetchCol($select);
        return reset($result);
    }

    /**
     * Load attachment
     *
     * @param int $attachmentId
     * @return array|bool
     */
    public function loadAttachment($attachmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::ATTACHMENT_TABLE_NAME))
            ->where('id = ?', $attachmentId);

        return $connection->fetchRow($select);
    }
}
