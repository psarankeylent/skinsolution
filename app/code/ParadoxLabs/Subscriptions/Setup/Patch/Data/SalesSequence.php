<?php declare(strict_types=1);
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class SalesSequence implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\SalesSequence\Model\MetaFactory
     */
    private $sequenceMetaFactory;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var \Magento\SalesSequence\Model\Builder
     */
    private $sequenceBuilder;

    /**
     * @var \Magento\SalesSequence\Model\Config
     */
    private $sequenceConfig;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\SalesSequence\Model\Builder $sequenceBuilder
     * @param \Magento\SalesSequence\Model\Config $sequenceConfig
     * @param \Magento\SalesSequence\Model\MetaFactory $sequenceMetaFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\SalesSequence\Model\Builder $sequenceBuilder,
        \Magento\SalesSequence\Model\Config $sequenceConfig,
        \Magento\SalesSequence\Model\MetaFactory $sequenceMetaFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeRepository = $storeRepository;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
        $this->sequenceMetaFactory = $sequenceMetaFactory;
    }

    /**
     * Run patch
     *
     * @return $this
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $this->createSalesSequence();

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Create subscription sales sequences for increment IDs.
     *
     * @return void
     */
    protected function createSalesSequence()
    {
        /** @var \Magento\SalesSequence\Model\ResourceModel\Meta $sequenceMetaResource */
        $sequenceMeta = $this->sequenceMetaFactory->create();
        $sequenceMetaResource = $sequenceMeta->getResource();

        $stores = $this->storeRepository->getList();

        foreach ($stores as $store) {
            $storeId = $store->getId();

            $sequenceMeta = $sequenceMetaResource->loadByEntityTypeAndStore(
                'subscription',
                $storeId
            );

            if ($sequenceMeta instanceof \Magento\SalesSequence\Model\Meta && !empty($sequenceMeta->getId())) {
                // Skip if sequence already exists for this store
                continue;
            }

            $this->sequenceBuilder->setPrefix($storeId ?: $this->sequenceConfig->get('prefix'))
                                  ->setSuffix($this->sequenceConfig->get('suffix'))
                                  ->setStartValue($this->sequenceConfig->get('startValue'))
                                  ->setStoreId($storeId)
                                  ->setStep($this->sequenceConfig->get('step'))
                                  ->setWarningValue($this->sequenceConfig->get('warningValue'))
                                  ->setMaxValue($this->sequenceConfig->get('maxValue'))
                                  ->setEntityType('subscription')
                                  ->create();
        }
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->startSetup();

        /** @var \Magento\SalesSequence\Model\ResourceModel\Meta $sequenceMetaResource */
        $sequenceMeta = $this->sequenceMetaFactory->create();
        $sequenceMetaResource = $sequenceMeta->getResource();

        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $sequenceMeta = $sequenceMetaResource->loadByEntityTypeAndStore(
                'subscription',
                $store->getId()
            );

            if ($sequenceMeta instanceof \Magento\SalesSequence\Model\Meta && !empty($sequenceMeta->getId())) {
                // Remove sequence table
                $this->moduleDataSetup->getConnection()->dropTable(
                    $this->moduleDataSetup->getTable($sequenceMeta->getData('sequence_table'))
                );

                // Remove sequence meta
                $sequenceMetaResource->delete($sequenceMeta);
            }
        }

        $this->moduleDataSetup->endSetup();
    }
}
