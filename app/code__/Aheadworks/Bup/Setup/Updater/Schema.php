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
namespace Aheadworks\Bup\Setup\Updater;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class Schema
 *
 * @package Aheadworks\Bup\Setup\Updater
 */
class Schema
{
    /**
     * Upgrade to version 1.0.1
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    public function upgradeTo101(SchemaSetupInterface $installer)
    {
        $this->addUserProfileSortOrderColumn($installer);

        return $this;
    }

    /**
     * Add sort_order column to 'aw_bup_user_profile' table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addUserProfileSortOrderColumn($installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable(UserProfileResource::MAIN_TABLE_NAME),
            UserProfileInterface::SORT_ORDER,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'unsigned' => true,
                'default' => 0,
                'after' => UserProfileInterface::STATUS,
                'comment' => 'Sort Order'
            ]
        );

        return $this;
    }
}
