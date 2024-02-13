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
namespace Aheadworks\Helpdesk2\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface as TicketGridInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Setup\Updater\Schema as SchemaUpdater;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\Helpdesk2\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param SchemaUpdater $updater
     */
    public function __construct(
        SchemaUpdater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createTicketStatusTable($installer)
            ->createTicketPriorityTable($installer);

        $this
            ->createEmailRejectingPatternTable($installer)
            ->createEmailRejectingPatternScopeTable($installer);

        $this
            ->createGatewayTable($installer)
            ->createGatewayEmailTable($installer)
            ->createGatewayEmailAttachmentTable($installer);

        $this
            ->createDepartmentTable($installer)
            ->createDepartmentStoreTable($installer)
            ->createDepartmentPermissionTable($installer)
            ->createDepartmentAgentTable($installer)
            ->createDepartmentGatewayTable($installer)
            ->createDepartmentOptionTable($installer)
            ->createDepartmentOptionTypeValueTable($installer);

        $this
            ->createTicketEntityTable($installer)
            ->createTicketEntityVarcharTable($installer)
            ->createTicketEntityIntTable($installer)
            ->createTicketEntityTextTable($installer)
            ->createTicketEavAttributeTable($installer)
            ->createTicketOptionTable($installer);

        $this
            ->createTicketMessageTable($installer)
            ->createTicketMessageAttachmentTable($installer);

        $this
            ->createQuickResponseTable($installer)
            ->createLabelTable($installer);

        $this
            ->createTicketGridTable($installer);

        $this
            ->createAutomationTable($installer)
            ->createAutomationTaskTable($installer);

        $this->updater->upgradeTo080($setup);
        $this->updater->upgradeTo100($setup);

        $installer->endSetup();
    }

    /**
     * Create table 'aw_helpdesk2_ticket_status'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketStatusTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(StatusResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                TicketStatusInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Status ID'
            )->addColumn(
                TicketStatusInterface::IS_SYSTEM,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Is Status System'
            )->addColumn(
                TicketStatusInterface::LABEL,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Status Label'
            )->setComment('Ticket Status Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_priority'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketPriorityTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(PriorityResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                TicketPriorityInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Priority ID'
            )->addColumn(
                TicketPriorityInterface::IS_SYSTEM,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Is Priority System'
            )->addColumn(
                TicketPriorityInterface::LABEL,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Priority Label'
            )->setComment('Ticket Priority Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_gateway'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createGatewayTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(GatewayResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                GatewayDataInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Gateway ID'
            )->addColumn(
                GatewayDataInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Gateway Active'
            )->addColumn(
                GatewayDataInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                GatewayDataInterface::DEFAULT_STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Default Store Id'
            )->addColumn(
                GatewayDataInterface::TYPE,
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Gateway Type'
            )->addColumn(
                GatewayDataInterface::GATEWAY_PROTOCOL,
                Table::TYPE_TEXT,
                64,
                ['nullable' => true],
                'Gateway Protocol'
            )->addColumn(
                GatewayDataInterface::HOST,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Gateway Host'
            )->addColumn(
                GatewayDataInterface::AUTHORIZATION_TYPE,
                Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'Authorization Type'
            )->addColumn(
                GatewayDataInterface::EMAIL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Email'
            )->addColumn(
                GatewayDataInterface::LOGIN,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Login'
            )->addColumn(
                GatewayDataInterface::PASSWORD,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Password'
            )->addColumn(
                GatewayDataInterface::CLIENT_ID,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Client ID'
            )->addColumn(
                GatewayDataInterface::CLIENT_SECRET,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Client Secret'
            )->addColumn(
                GatewayDataInterface::SECURITY_PROTOCOL,
                Table::TYPE_TEXT,
                10,
                ['nullable' => true],
                'Security protocol'
            )->addColumn(
                GatewayDataInterface::PORT,
                Table::TYPE_TEXT,
                10,
                ['nullable' => true],
                'Port'
            )->addColumn(
                GatewayDataInterface::ACCESS_TOKEN,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Access Token'
            )->addColumn(
                GatewayDataInterface::IS_VERIFIED,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => true],
                'Is Verified'
            )->addColumn(
                GatewayDataInterface::IS_DELETE_FROM_HOST,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Delete From Host'
            )->setComment('Gateway Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_gateway_email'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createGatewayEmailTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(GatewayEmailResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                EmailInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'ID'
            )->addColumn(
                EmailInterface::UID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Mail UID'
            )->addColumn(
                EmailInterface::GATEWAY_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Gateway ID'
            )->addColumn(
                EmailInterface::FROM,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email Sender'
            )->addColumn(
                EmailInterface::TO,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email Recipient'
            )->addColumn(
                EmailInterface::STATUS,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Mail Status'
            )->addColumn(
                EmailInterface::SUBJECT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Mail Subject'
            )->addColumn(
                EmailInterface::BODY,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Mail Body'
            )->addColumn(
                EmailInterface::HEADERS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Mail Headers'
            )->addColumn(
                EmailInterface::CONTENT_TYPE,
                Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Mail Content Type'
            )->addColumn(
                EmailInterface::REJECT_PATTERN_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Reject Pattern ID'
            )->addColumn(
                EmailInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At Date'
            )->addIndex(
                $setup->getIdxName(GatewayEmailResourceModel::MAIN_TABLE_NAME, [EmailInterface::STATUS]),
                [EmailInterface::STATUS]
            )->addForeignKey(
                $setup->getFkName(
                    GatewayEmailResourceModel::MAIN_TABLE_NAME,
                    EmailInterface::GATEWAY_ID,
                    GatewayResourceModel::MAIN_TABLE_NAME,
                    GatewayDataInterface::ID
                ),
                EmailInterface::GATEWAY_ID,
                $setup->getTable(GatewayResourceModel::MAIN_TABLE_NAME),
                GatewayDataInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    GatewayEmailResourceModel::MAIN_TABLE_NAME,
                    EmailInterface::REJECT_PATTERN_ID,
                    RejectingPatternResourceModel::MAIN_TABLE_NAME,
                    RejectingPatternInterface::ID
                ),
                EmailInterface::REJECT_PATTERN_ID,
                $setup->getTable(RejectingPatternResourceModel::MAIN_TABLE_NAME),
                RejectingPatternInterface::ID,
                Table::ACTION_SET_NULL
            )->setComment('Gateway Email Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_gateway_email_attachment'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createGatewayEmailAttachmentTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(GatewayEmailResourceModel::EMAIL_ATTACHMENT_TABLE_NAME))
            ->addColumn(
                EmailAttachmentInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Attachment ID'
            )->addColumn(
                EmailAttachmentInterface::EMAIL_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Message ID'
            )->addColumn(
                EmailAttachmentInterface::FILE_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'File name'
            )->addColumn(
                EmailAttachmentInterface::FILE_PATH,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'File path'
            )->addIndex(
                $setup->getIdxName(
                    GatewayEmailResourceModel::EMAIL_ATTACHMENT_TABLE_NAME,
                    [EmailAttachmentInterface::EMAIL_ID]
                ),
                [EmailAttachmentInterface::EMAIL_ID]
            )->addForeignKey(
                $setup->getFkName(
                    GatewayEmailResourceModel::EMAIL_ATTACHMENT_TABLE_NAME,
                    EmailAttachmentInterface::EMAIL_ID,
                    GatewayEmailResourceModel::MAIN_TABLE_NAME,
                    EmailInterface::ID
                ),
                EmailAttachmentInterface::EMAIL_ID,
                $setup->getTable(GatewayEmailResourceModel::MAIN_TABLE_NAME),
                EmailInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Gateway Email Attachment Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_email_rejecting_pattern'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createEmailRejectingPatternTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RejectingPatternResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                RejectingPatternInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Pattern ID'
            )->addColumn(
                RejectingPatternInterface::TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Title'
            )->addColumn(
                RejectingPatternInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Pattern Active'
            )->addColumn(
                RejectingPatternInterface::PATTERN,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Pattern'
            )->setComment('Email Rejecting Pattern Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_email_rejecting_pattern_scope'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createEmailRejectingPatternScopeTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RejectingPatternResourceModel::SCOPE_TABLE))
            ->addColumn(
                'pattern_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pattern ID'
            )->addColumn(
                'scope',
                Table::TYPE_TEXT,
                16,
                ['nullable' => false, 'primary' => true],
                'Scope'
            )->addForeignKey(
                $setup->getFkName(
                    RejectingPatternResourceModel::SCOPE_TABLE,
                    'pattern_id',
                    RejectingPatternResourceModel::MAIN_TABLE_NAME,
                    RejectingPatternInterface::ID
                ),
                'pattern_id',
                $setup->getTable(RejectingPatternResourceModel::MAIN_TABLE_NAME),
                RejectingPatternInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Pattern to Scope Type Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                DepartmentInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Department ID'
            )->addColumn(
                DepartmentInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                DepartmentInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Department Active'
            )->addColumn(
                DepartmentInterface::PRIMARY_AGENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Primary Agent ID'
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::PRIMARY_AGENT_ID,
                    'admin_user',
                    'user_id'
                ),
                DepartmentInterface::PRIMARY_AGENT_ID,
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_SET_NULL
            )->setComment('Department Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_store'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentStoreTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME))
            ->addColumn(
                'department_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Department ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $setup->getIdxName(DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME, ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME,
                    'department_id',
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                'department_id',
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Department to Store Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_permission'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentPermissionTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME))
            ->addColumn(
                'department_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Department ID'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false, 'primary' => true],
                'Type'
            )->addColumn(
                'role_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Role ID'
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME,
                    'department_id',
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                'department_id',
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Department to Permission Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_agent'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentAgentTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME))
            ->addColumn(
                'department_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Department ID'
            )->addColumn(
                'agent_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Agent ID'
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME,
                    'department_id',
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                'department_id',
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME,
                    'agent_id',
                    'admin_user',
                    'user_id'
                ),
                'agent_id',
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_CASCADE
            )->setComment('Department to Store Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_gateway'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentGatewayTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME))
            ->addColumn(
                'department_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Department ID'
            )->addColumn(
                'gateway_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Gateway ID'
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME,
                    'department_id',
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                'department_id',
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME,
                    'gateway_id',
                    GatewayResourceModel::MAIN_TABLE_NAME,
                    GatewayDataInterface::ID
                ),
                'gateway_id',
                $setup->getTable(GatewayResourceModel::MAIN_TABLE_NAME),
                GatewayDataInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Department to Gateway Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_option'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentOptionTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME))
            ->addColumn(
                DepartmentOptionInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'identity' => true],
                'ID'
            )->addColumn(
                DepartmentOptionInterface::DEPARTMENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Department ID'
            )->addColumn(
                DepartmentOptionInterface::TYPE,
                Table::TYPE_TEXT,
                80,
                ['nullable' => false],
                'Type'
            )->addColumn(
                DepartmentOptionInterface::IS_REQUIRED,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Option Required'
            )->addColumn(
                DepartmentOptionInterface::SORT_ORDER,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName(
                    DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME,
                    [DepartmentOptionInterface::DEPARTMENT_ID]
                ),
                [DepartmentOptionInterface::DEPARTMENT_ID]
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME,
                    DepartmentOptionInterface::DEPARTMENT_ID,
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                DepartmentOptionInterface::DEPARTMENT_ID,
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Department to Option Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_department_option_type_value'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createDepartmentOptionTypeValueTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DepartmentResourceModel::DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME))
            ->addColumn(
                DepartmentOptionValueInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'identity' => true],
                'ID'
            )->addColumn(
                DepartmentOptionValueInterface::OPTION_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Option ID'
            )->addColumn(
                DepartmentOptionValueInterface::SORT_ORDER,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName(
                    DepartmentResourceModel::DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME,
                    [DepartmentOptionValueInterface::OPTION_ID]
                ),
                [DepartmentOptionValueInterface::OPTION_ID]
            )->addForeignKey(
                $setup->getFkName(
                    DepartmentResourceModel::DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME,
                    DepartmentOptionValueInterface::OPTION_ID,
                    DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME,
                    DepartmentOptionInterface::ID
                ),
                DepartmentOptionValueInterface::OPTION_ID,
                $setup->getTable(DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME),
                DepartmentOptionInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Option to Option Type Value Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_entity'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketEntityTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME))
            ->addColumn(
                TicketInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Entity ID'
            )->addColumn(
                TicketInterface::UID,
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Ticket Unique ID'
            )->addColumn(
                TicketInterface::RATING,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Rating'
            )->addColumn(
                TicketInterface::SUBJECT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Subject'
            )->addColumn(
                TicketInterface::DEPARTMENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Department ID'
            )->addColumn(
                TicketInterface::AGENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Agent ID'
            )->addColumn(
                TicketInterface::STORE_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store ID'
            )->addColumn(
                TicketInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Customer ID'
            )->addColumn(
                TicketInterface::CUSTOMER_EMAIL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                TicketInterface::CUSTOMER_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer Name'
            )->addColumn(
                TicketInterface::CC_RECIPIENTS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'CC Recipients'
            )->addColumn(
                TicketInterface::STATUS_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status ID'
            )->addColumn(
                TicketInterface::PRIORITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Priority ID'
            )->addColumn(
                TicketInterface::INTERNAL_NOTE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Internal Note'
            )->addColumn(
                TicketInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At Date'
            )->addColumn(
                TicketInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At Date'
            )->addColumn(
                TicketInterface::INTERNAL_NOTE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Internal Note'
            )->addColumn(
                TicketInterface::EXTERNAL_LINK,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'External Link'
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_TABLE_NAME, [TicketInterface::DEPARTMENT_ID]),
                [TicketInterface::DEPARTMENT_ID]
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_TABLE_NAME, [TicketInterface::CUSTOMER_ID]),
                [TicketInterface::CUSTOMER_ID]
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_TABLE_NAME, [TicketInterface::CUSTOMER_EMAIL]),
                [TicketInterface::CUSTOMER_EMAIL]
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::STATUS_ID,
                    StatusResourceModel::MAIN_TABLE_NAME,
                    TicketStatusInterface::ID
                ),
                TicketInterface::STATUS_ID,
                $setup->getTable(StatusResourceModel::MAIN_TABLE_NAME),
                TicketStatusInterface::ID,
                Table::ACTION_RESTRICT
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::PRIORITY_ID,
                    PriorityResourceModel::MAIN_TABLE_NAME,
                    TicketPriorityInterface::ID
                ),
                TicketInterface::PRIORITY_ID,
                $setup->getTable(PriorityResourceModel::MAIN_TABLE_NAME),
                TicketPriorityInterface::ID,
                Table::ACTION_RESTRICT
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::DEPARTMENT_ID,
                    DepartmentResourceModel::MAIN_TABLE_NAME,
                    DepartmentInterface::ID
                ),
                TicketInterface::DEPARTMENT_ID,
                $setup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
                DepartmentInterface::ID,
                Table::ACTION_RESTRICT
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                TicketInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_SET_NULL
            )->setComment('Ticket Entity Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_entity_varchar'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketEntityVarcharTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME,
                    ['entity_id', 'attribute_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME, ['attribute_id']),
                ['attribute_id']
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME, ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME,
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME,
                    'entity_id',
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Ticket Entity Varchar Attribute Backend Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_entity_int'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketEntityIntTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )->addColumn(
                'value',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME,
                    ['entity_id', 'attribute_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME, ['attribute_id']),
                ['attribute_id']
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME, ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME,
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME,
                    'entity_id',
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Ticket Entity Int Attribute Backend Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_entity_text'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketEntityTextTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME,
                    ['entity_id', 'attribute_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME, ['attribute_id']),
                ['attribute_id']
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME, ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME,
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME,
                    'entity_id',
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Ticket Entity Text Attribute Backend Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_eav_attribute'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketEavAttributeTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_EAV_ATTRIBUTE_TABLE_NAME))
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute ID'
            )->addColumn(
                'is_global',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Attribute Global'
            )->addColumn(
                'is_visible_in_grid',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Visible in Grid'
            )->addColumn(
                'is_filterable_in_grid',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable in Grid'
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_EAV_ATTRIBUTE_TABLE_NAME,
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->setComment('Ticket EAV Attribute Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_option'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketOptionTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketResourceModel::TICKET_OPTION_TABLE_NAME))
            ->addColumn(
                TicketOptionInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Option ID'
            )->addColumn(
                TicketOptionInterface::TICKET_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Ticket ID'
            )->addColumn(
                TicketOptionInterface::LABEL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Option Label'
            )->addColumn(
                TicketOptionInterface::VALUE,
                Table::TYPE_TEXT,
                null,
                [ 'nullable' => false],
                'Option Value'
            )->addIndex(
                $setup->getIdxName(TicketResourceModel::TICKET_OPTION_TABLE_NAME, [TicketOptionInterface::TICKET_ID]),
                [TicketOptionInterface::TICKET_ID]
            )->addForeignKey(
                $setup->getFkName(
                    TicketResourceModel::TICKET_OPTION_TABLE_NAME,
                    TicketOptionInterface::TICKET_ID,
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::ENTITY_ID
                ),
                TicketOptionInterface::TICKET_ID,
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                TicketInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Ticket Option Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_message_attachment'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketMessageAttachmentTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(MessageResourceModel::ATTACHMENT_TABLE_NAME))
            ->addColumn(
                MessageAttachmentInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Attachment ID'
            )->addColumn(
                MessageAttachmentInterface::MESSAGE_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Message ID'
            )->addColumn(
                MessageAttachmentInterface::FILE_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'File name'
            )->addColumn(
                MessageAttachmentInterface::FILE_PATH,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'File path'
            )->addIndex(
                $setup->getIdxName(
                    MessageResourceModel::ATTACHMENT_TABLE_NAME,
                    [MessageAttachmentInterface::MESSAGE_ID]
                ),
                [MessageAttachmentInterface::MESSAGE_ID]
            )->addForeignKey(
                $setup->getFkName(
                    MessageResourceModel::ATTACHMENT_TABLE_NAME,
                    MessageAttachmentInterface::MESSAGE_ID,
                    MessageResourceModel::MAIN_TABLE_NAME,
                    MessageInterface::ID
                ),
                MessageAttachmentInterface::MESSAGE_ID,
                $setup->getTable(MessageResourceModel::MAIN_TABLE_NAME),
                MessageInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Ticket Message Attachment Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_message'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketMessageTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(MessageResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                MessageInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Message ID'
            )->addColumn(
                MessageInterface::TICKET_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Ticket ID'
            )->addColumn(
                MessageInterface::CONTENT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Message content'
            )->addColumn(
                MessageInterface::TYPE,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Message type'
            )->addColumn(
                MessageInterface::AUTHOR_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Author name'
            )->addColumn(
                MessageInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At Date'
            )->addIndex(
                $setup->getIdxName(
                    MessageResourceModel::MAIN_TABLE_NAME,
                    [MessageInterface::TICKET_ID, MessageInterface::TYPE],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                [MessageInterface::TICKET_ID, MessageInterface::TYPE],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            )->addIndex(
                $setup->getIdxName(MessageResourceModel::MAIN_TABLE_NAME, [MessageInterface::TICKET_ID]),
                [MessageInterface::TICKET_ID]
            )->addIndex(
                $setup->getIdxName(MessageResourceModel::MAIN_TABLE_NAME, [MessageInterface::TYPE]),
                [MessageInterface::TYPE]
            )->addIndex(
                $setup->getIdxName(MessageResourceModel::MAIN_TABLE_NAME, [MessageInterface::CREATED_AT]),
                [MessageInterface::CREATED_AT]
            )->addForeignKey(
                $setup->getFkName(
                    MessageResourceModel::MAIN_TABLE_NAME,
                    MessageInterface::TICKET_ID,
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::ENTITY_ID
                ),
                MessageInterface::TICKET_ID,
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                TicketInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Ticket Message Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_quick_response'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createQuickResponseTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(QuickResponseResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                QuickResponseInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quick Response ID'
            )->addColumn(
                QuickResponseInterface::TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Quick Response Title'
            )->addColumn(
                AutomationInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Quick Response Active'
            )->addColumn(
                QuickResponseInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At Date'
            )->addColumn(
                QuickResponseInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At Date'
            )->setComment('Quick Response Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_label'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createLabelTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_helpdesk2_label'))
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'entity_type',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Entity Type'
            )->addColumn(
                'content',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Content'
            )->addIndex(
                $setup->getIdxName('aw_helpdesk2_label', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'aw_helpdesk2_label',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Label Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_ticket_grid'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTicketGridTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TicketGridResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                TicketGridInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Entity ID'
            )->addColumn(
                TicketGridInterface::UID,
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Ticket Unique ID'
            )->addColumn(
                TicketGridInterface::RATING,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Rating'
            )->addColumn(
                TicketGridInterface::LAST_MESSAGE_DATE,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Last Message Date'
            )->addColumn(
                TicketGridInterface::LAST_MESSAGE_BY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Last Message By'
            )->addColumn(
                TicketGridInterface::LAST_MESSAGE_TYPE,
                Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Last Message Type'
            )->addColumn(
                TicketGridInterface::DEPARTMENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Department ID'
            )->addColumn(
                TicketGridInterface::AGENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Agent ID'
            )->addColumn(
                TicketGridInterface::SUBJECT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Subject'
            )->addColumn(
                TicketGridInterface::FIRST_MESSAGE_CONTENT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'First Message Content'
            )->addColumn(
                TicketGridInterface::LAST_MESSAGE_CONTENT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Last Message Content'
            )->addColumn(
                TicketGridInterface::CUSTOMER_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer name'
            )->addColumn(
                TicketInterface::PRIORITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Priority ID'
            )->addColumn(
                TicketGridInterface::CUSTOMER_MESSAGE_COUNT,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Customer Message Count'
            )->addColumn(
                TicketGridInterface::AGENT_MESSAGE_COUNT,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Agent Message Count'
            )->addColumn(
                TicketGridInterface::MESSAGE_COUNT,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Message Count'
            )->addColumn(
                TicketGridInterface::STATUS_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status ID'
            )->addColumn(
                TicketGridInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At Date'
            )->addColumn(
                TicketInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At Date'
            )->addIndex(
                $setup->getIdxName(
                    TicketGridResourceModel::MAIN_TABLE_NAME,
                    [TicketGridInterface::SUBJECT, TicketGridInterface::CUSTOMER_NAME],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [TicketGridInterface::SUBJECT, TicketGridInterface::CUSTOMER_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->addForeignKey(
                $setup->getFkName(
                    TicketGridResourceModel::MAIN_TABLE_NAME,
                    TicketGridInterface::ENTITY_ID,
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::ENTITY_ID
                ),
                TicketGridInterface::ENTITY_ID,
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                TicketInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->setComment('Ticket Grid Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_automation'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createAutomationTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(AutomationResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                AutomationInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Automation ID'
            )->addColumn(
                AutomationInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Automation name'
            )->addColumn(
                AutomationInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Automation Active'
            )->addColumn(
                AutomationInterface::PRIORITY,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Priority'
            )->addColumn(
                AutomationInterface::IS_REQUIRED_TO_BREAK,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Required to Break Chain of Automations'
            )->addColumn(
                AutomationInterface::EVENT,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Event'
            )->addColumn(
                AutomationInterface::CONDITIONS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Serialized Conditions'
            )->addColumn(
                AutomationInterface::ACTIONS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Serialized Actions'
            )->setComment('Automation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_helpdesk2_automation_task'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createAutomationTaskTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(TaskResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                TaskInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Task ID'
            )->addColumn(
                TaskInterface::AUTOMATION_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Automation ID'
            )->addColumn(
                TaskInterface::TICKET_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ticket ID'
            )->addColumn(
                TaskInterface::ACTION_TYPE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Action Type'
            )->addColumn(
                TaskInterface::ACTION_DATA,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Action Data'
            )->addColumn(
                TaskInterface::STATUS,
                Table::TYPE_TEXT,
                12,
                ['nullable' => false],
                'Status'
            )->addForeignKey(
                $setup->getFkName(
                    TaskResourceModel::MAIN_TABLE_NAME,
                    TaskInterface::TICKET_ID,
                    TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                    TicketInterface::ENTITY_ID
                ),
                TaskInterface::TICKET_ID,
                $setup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
                TicketInterface::ENTITY_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    TaskResourceModel::MAIN_TABLE_NAME,
                    TaskInterface::AUTOMATION_ID,
                    AutomationResourceModel::MAIN_TABLE_NAME,
                    AutomationInterface::ID
                ),
                TaskInterface::AUTOMATION_ID,
                $setup->getTable(AutomationResourceModel::MAIN_TABLE_NAME),
                AutomationInterface::ID,
                Table::ACTION_CASCADE
            )->setComment('Automation Task Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
