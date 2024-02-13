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
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Aheadworks\Bup\Setup\Updater\Schema as SchemaUpdater;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Bup\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param SchemaUpdater $updater
     */
    public function __construct(SchemaUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updater->upgradeTo101($setup);
        }
    }
}
