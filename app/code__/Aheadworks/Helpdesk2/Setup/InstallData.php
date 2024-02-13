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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\Helpdesk2\Model\SampleData\Installer as SampleDataInstaller;

/**
 * Class InstallData
 *
 * @package Aheadworks\Helpdesk2\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var SampleDataExecutor
     */
    private $sampleDataExecutor;

    /**
     * @var SampleDataInstaller
     */
    private $sampleDataInstaller;

    /**
     * @var TicketEavSetupFactory
     */
    private $ticketEavSetupFactory;

    /**
     * @param SampleDataExecutor $sampleDataExecutor
     * @param SampleDataInstaller $sampleDataInstaller
     * @param TicketEavSetupFactory $ticketEavSetupFactory
     */
    public function __construct(
        SampleDataExecutor $sampleDataExecutor,
        SampleDataInstaller $sampleDataInstaller,
        TicketEavSetupFactory $ticketEavSetupFactory
    ) {
        $this->sampleDataExecutor = $sampleDataExecutor;
        $this->sampleDataInstaller = $sampleDataInstaller;
        $this->ticketEavSetupFactory = $ticketEavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->installTicketAttributes($setup)
            ->installSampleData();
    }

    /**
     * Install ticket attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function installTicketAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var TicketEavSetup $ticketEavSetup */
        $ticketEavSetup = $this->ticketEavSetupFactory->create(['setup' => $setup]);
        $ticketEavSetup->installEntities();

        return $this;
    }

    /**
     * Install sample data
     *
     * @return $this
     */
    private function installSampleData()
    {
        $this->sampleDataExecutor->exec($this->sampleDataInstaller);

        return $this;
    }
}
