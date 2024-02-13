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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;
use Aheadworks\Bup\Setup\Updater\Schema as SchemaUpdater;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\Bup\Setup
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

        $this->createUserProfileTable($installer);

        $this->updater->upgradeTo101($setup);

        $installer->endSetup();
    }

    /**
     * Create table 'aw_bup_user_profile'
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createUserProfileTable($installer)
    {
        $salesRepTable = $installer->getConnection()
            ->newTable($installer->getTable(UserProfileResource::MAIN_TABLE_NAME))
            ->addColumn(
                UserProfileInterface::USER_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'User ID'
            )->addColumn(
                UserProfileInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Status'
            )->addColumn(
                UserProfileInterface::DISPLAY_NAME,
                Table::TYPE_TEXT,
                null,
                [],
                'Display name'
            )->addColumn(
                UserProfileInterface::EMAIL,
                Table::TYPE_TEXT,
                null,
                [],
                'Email'
            )->addColumn(
                UserProfileInterface::PHONE_NUMBER,
                Table::TYPE_TEXT,
                null,
                [],
                'Phone number'
            )->addColumn(
                UserProfileInterface::IMAGE,
                Table::TYPE_TEXT,
                null,
                [],
                'Image'
            )->addColumn(
                UserProfileInterface::ADDITIONAL_INFORMATION,
                Table::TYPE_TEXT,
                null,
                [],
                'Additional information'
            )->addForeignKey(
                $installer->getFkName(
                    UserProfileResource::MAIN_TABLE_NAME,
                    UserProfileInterface::USER_ID,
                    'admin_user',
                    'user_id'
                ),
                UserProfileInterface::USER_ID,
                $installer->getTable('admin_user'),
                'user_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($salesRepTable);

        return $this;
    }
}
