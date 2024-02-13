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
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Aheadworks\Helpdesk2\Setup\Updater\Schema as SchemaUpdater;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Ca\Setup
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
        if ($context->getVersion() && version_compare($context->getVersion(), '0.8.0', '<')) {
            $this->updater->upgradeTo080($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->updater->upgradeTo100($setup);
        }
    }
}
