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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Repository as StorefrontLabelRepository;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage as RejectedMessageResourceModel;

/**
 * Class Uninstall
 *
 * @package Aheadworks\Helpdesk2\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @inheritdoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallTicketEavData($installer)
            ->uninstallConfigData($installer);

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        $connection->dropTable($installer->getTable(StatusResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(PriorityResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(TagResourceModel::RELATION_TABLE_NAME));
        $connection->dropTable($installer->getTable(TagResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_OPTION_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketResourceModel::TICKET_EAV_ATTRIBUTE_TABLE_NAME));

        $connection->dropTable($installer->getTable(MessageResourceModel::ATTACHMENT_TABLE_NAME));
        $connection->dropTable($installer->getTable(MessageResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(RejectingPatternResourceModel::SCOPE_TABLE));
        $connection->dropTable($installer->getTable(RejectingPatternResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(GatewayResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(GatewayEmailResourceModel::EMAIL_ATTACHMENT_TABLE_NAME));
        $connection->dropTable($installer->getTable(GatewayEmailResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_OPTION_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME));
        $connection->dropTable($installer->getTable(DepartmentResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(QuickResponseResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(StorefrontLabelRepository::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(TicketGridResourceModel::MAIN_TABLE_NAME));

        $connection->dropTable($installer->getTable(TaskResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(AutomationResourceModel::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(RejectedMessageResourceModel::MAIN_TABLE_NAME));

        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_helpdesk2%'");
        return $this;
    }

    /**
     * Uninstall ticket eav data with all attributes
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTicketEavData(SchemaSetupInterface $installer)
    {
        $select = $installer->getConnection()->select()
            ->from($installer->getTable('eav_entity_type'), 'entity_type_id')
            ->where('entity_type_code = ?', TicketModel::ENTITY);
        $entityTypeId = $installer->getConnection()->fetchCol($select);

        $condition = ['entity_type_id = ?' => $entityTypeId];
        $installer->getConnection()->delete($installer->getTable('eav_entity_type'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_attribute'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_entity_attribute'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_attribute_set'), $condition);
        return $this;
    }
}
