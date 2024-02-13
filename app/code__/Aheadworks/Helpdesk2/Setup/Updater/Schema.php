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
namespace Aheadworks\Helpdesk2\Setup\Updater;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage as RejectedMessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface as TicketGridInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketTagInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResource;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResource;

/**
 * Class Schema
 *
 * @package Aheadworks\Helpdesk2\Setup\Updater
 */
class Schema
{
    /**
     * Upgrade to version 0.8.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function upgradeTo080(SchemaSetupInterface $installer)
    {
        $this->addCcRecipientsColumn($installer);
        $this->addCustomerIdAndEmailColumns($installer);
        $this->createTagTable($installer);
        $this->createTicketTagTable($installer);
        $this->addCustomerRatingColumn($installer);

        return $this;
    }

    /**
     * Upgrade to version 1.0.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function upgradeTo100(SchemaSetupInterface $installer)
    {
        $this->addOrderIdColumn($installer);
        $this->addTicketMessageIdColumn($installer);
        $this->addGatewayIdColumn($installer);
        $this->addAllowGuestsColumn($installer);
        $this->addQuickResponseSortOrderColumn($installer);
        $this->addDepartmentSortOrderColumn($installer);
        $this->createRejectedMessageTable($installer);
        $this->addIsTicketLockedColumn($installer);
        $this->updateTicketFulltextIndex($installer);

        return $this;
    }

    /**
     * Add cc recipients column to 'aw_helpdesk_gateway_email' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCcRecipientsColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(GatewayEmailResourceModel::MAIN_TABLE_NAME),
            EmailInterface::CC_RECIPIENTS,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => null,
                'after' => EmailInterface::TO,
                'comment' => 'CC Recipients'
            ]
        );

        return $this;
    }

    /**
     * Add customer_id and customer_email columns to 'aw_helpdesk_ticket_grid' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCustomerIdAndEmailColumns($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            TicketGridInterface::CUSTOMER_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'after' => TicketGridInterface::CUSTOMER_NAME,
                'comment' => 'Customer ID'
            ]
        );
        $connection->addColumn(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            TicketGridInterface::CUSTOMER_EMAIL,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 255,
                'after' => TicketGridInterface::CUSTOMER_ID,
                'comment' => 'Customer Email'
            ]
        );

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_tag'
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTagTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(TagResource::MAIN_TABLE_NAME))
            ->addColumn(
                TagInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag Id'
            )->addColumn(
                TagInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                TagInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                TagInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addIndex(
                $installer->getIdxName(TagResource::MAIN_TABLE_NAME, [TagInterface::NAME]),
                [TagInterface::NAME]
            )->setComment('Helpdesk2 Tag');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_tag'
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketTagTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(TagResource::RELATION_TABLE_NAME))
            ->addColumn(
                TicketTagInterface::TAG_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag Id'
            )->addColumn(
                TicketTagInterface::TICKET_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Ticket Id'
            )->addIndex(
                $installer->getIdxName(TagResource::RELATION_TABLE_NAME, [TicketTagInterface::TAG_ID]),
                [TicketTagInterface::TAG_ID]
            )->addIndex(
                $installer->getIdxName(TagResource::RELATION_TABLE_NAME, [TicketTagInterface::TICKET_ID]),
                [TicketTagInterface::TICKET_ID]
            )->addForeignKey(
                $installer->getFkName(
                    TagResource::RELATION_TABLE_NAME,
                    TicketTagInterface::TAG_ID,
                    TagResource::MAIN_TABLE_NAME,
                    TagInterface::ID
                ),
                TicketTagInterface::TAG_ID,
                $installer->getTable(TagResource::MAIN_TABLE_NAME),
                TagInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    TagResource::RELATION_TABLE_NAME,
                    TicketTagInterface::TICKET_ID,
                    TicketResource::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::ENTITY_ID
                ),
                TicketTagInterface::TICKET_ID,
                $installer->getTable(TicketResource::TICKET_ENTITY_TABLE_NAME),
                TicketInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Ticket To Tag Relation Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add customer_rating column to 'aw_helpdesk_ticket_entity' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCustomerRatingColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(TicketResource::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::CUSTOMER_RATING,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'after' => TicketInterface::RATING,
                'comment' => 'Customer Rating'
            ]
        );

        return $this;
    }

    /**
     * Add order_id column to 'aw_helpdesk_ticket_entity' and 'aw_helpdesk_ticket_grid' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addOrderIdColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(TicketResource::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::ORDER_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'after' => TicketInterface::PRIORITY_ID,
                'comment' => 'Order Id'
            ]
        );
        $connection->addColumn(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            TicketGridInterface::ORDER_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'after' => TicketGridInterface::PRIORITY_ID,
                'comment' => 'Order Id'
            ]
        );
        $connection->addColumn(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            TicketGridInterface::ORDER_INCREMENT_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'size' => 32,
                'after' => TicketGridInterface::PRIORITY_ID,
                'comment' => 'Order Increment Id'
            ]
        );

        return $this;
    }

    /**
     * Add gateway_id column to 'aw_helpdesk_ticket_message' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addGatewayIdColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(MessageResourceModel::MAIN_TABLE_NAME),
            MessageInterface::GATEWAY_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'after' => MessageInterface::TICKET_ID,
                'comment' => 'Gateway Id'
            ]
        );

        return $this;
    }

    /**
     * Add ticket_message_id column to 'aw_helpdesk_gateway_email' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addTicketMessageIdColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(GatewayEmailResourceModel::MAIN_TABLE_NAME),
            EmailInterface::TICKET_MESSAGE_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'after' => EmailInterface::CREATED_AT,
                'comment' => 'Ticket message id'
            ]
        );

        return $this;
    }

    /**
     * Add is_allow_guest column to 'aw_helpdesk_department' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addAllowGuestsColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
            DepartmentInterface::IS_ALLOW_GUEST,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 1,
                'after' => DepartmentInterface::IS_ACTIVE,
                'comment' => 'Is Allow Guest'
            ]
        );

        return $this;
    }

    /**
     * Add sort_order column to 'aw_helpdesk_quick_response' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addQuickResponseSortOrderColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(QuickResponseResourceModel::MAIN_TABLE_NAME),
            QuickResponseInterface::SORT_ORDER,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'unsigned' => true,
                'default' => 0,
                'after' => QuickResponseInterface::UPDATED_AT,
                'comment' => 'Sort Order'
            ]
        );

        return $this;
    }

    /**
     * Add sort_order column to 'aw_helpdesk2_department' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addDepartmentSortOrderColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
            DepartmentInterface::SORT_ORDER,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'unsigned' => true,
                'default' => 0,
                'after' => DepartmentInterface::IS_ALLOW_GUEST,
                'comment' => 'Sort Order'
            ]
        );

        return $this;
    }

    /**
     * Add is_locked column to 'aw_helpdesk_ticket_entity' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addIsTicketLockedColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(TicketResource::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::IS_LOCKED,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'after' => TicketInterface::AGENT_ID,
                'comment' => 'Is Ticket Locked'
            ]
        );

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_rejected_message'
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRejectedMessageTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(RejectedMessageResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                RejectedMessageInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                RejectedMessageInterface::TYPE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Type'
            )->addColumn(
                RejectedMessageInterface::FROM,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'From'
            )->addColumn(
                RejectedMessageInterface::SUBJECT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Subject'
            )->addColumn(
                RejectedMessageInterface::CONTENT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Content'
            )->addColumn(
                RejectedMessageInterface::REJECT_PATTERN_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Reject Pattern ID'
            )->addColumn(
                RejectedMessageInterface::MESSAGE_DATA,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Json Message Data'
            )->addColumn(
                TagInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->setComment('Helpdesk2 Rejected Message');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Update fulltext index in 'aw_helpdesk_ticket_grid' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function updateTicketFulltextIndex($installer)
    {
        $connection = $installer->getConnection();
        $connection->dropIndex(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            $installer->getIdxName(
                TicketGridResourceModel::MAIN_TABLE_NAME,
                [TicketGridInterface::SUBJECT, TicketGridInterface::CUSTOMER_NAME],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            )
        );
        $connection->addIndex(
            $installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME),
            $installer->getIdxName(
                TicketGridResourceModel::MAIN_TABLE_NAME,
                [TicketGridInterface::UID, TicketGridInterface::SUBJECT, TicketGridInterface::CUSTOMER_NAME],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [TicketGridInterface::UID, TicketGridInterface::SUBJECT, TicketGridInterface::CUSTOMER_NAME],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        return $this;
    }
}
