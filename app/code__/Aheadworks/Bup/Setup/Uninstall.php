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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class Uninstall
 *
 * @package Aheadworks\Bup\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $dataSetup;

    /**
     * @param ModuleDataSetupInterface $dataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $dataSetup
    ) {
        $this->dataSetup = $dataSetup;
    }

    /**
     * @inheritdoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->uninstallTables($installer);

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
        $connection->dropTable($installer->getTable(UserProfileResource::MAIN_TABLE_NAME));

        return $this;
    }
}
