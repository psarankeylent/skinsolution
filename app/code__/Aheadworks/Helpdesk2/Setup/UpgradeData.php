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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Helpdesk2\Setup\Updater\Data as DataUpdater;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Helpdesk2\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var DataUpdater
     */
    private $updater;

    /**
     * @param DataUpdater $updater
     */
    public function __construct(
        DataUpdater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->updater->upgradeTo100($setup);
        }

        $setup->endSetup();
    }
}
