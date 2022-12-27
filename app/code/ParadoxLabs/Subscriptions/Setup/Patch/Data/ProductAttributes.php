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

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use ParadoxLabs\Subscriptions\Model\Attribute\Backend\Intervals;
use ParadoxLabs\Subscriptions\Model\Source\Period;

class ProductAttributes implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    private $productAction;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $productAction
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productAction = $productAction;
    }

    /**
     * Run patch
     *
     * @return $this
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        try {
            $this->createProductAttributes();
        } catch (\Exception $exception) {
            // No-op
        }

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
     * Create subscription product attributes.
     *
     * @return void
     */
    protected function createProductAttributes()
    {
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Attributes:
         * subscription_active
         * subscription_allow_onetime
         * subscription_intervals
         * subscription_unit
         * subscription_length
         * subscription_price
         * subscription_init_adjustment
         */

        // Add new tab
        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, 'Subscription', 65);
        $categorySetup->updateAttributeGroup(
            $entityTypeId,
            $attributeSetId,
            'Subscription',
            'attribute_group_code',
            'subscription'
        );
        $categorySetup->updateAttributeGroup(
            $entityTypeId,
            $attributeSetId,
            'Subscription',
            'tab_group_code',
            'advanced'
        );

        $exists = $categorySetup->getAttributeId(Product::ENTITY, 'subscription_active');

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_active',
            [
                'type'                  => 'int',
                'label'                 => 'Enable',
                'input'                 => 'select',
                'source'                => Boolean::class,
                'sort_order'            => 100,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => null,
                'group'                 => 'Subscription',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'used_for_promo_rules'  => true,
                'required'              => false,
                'default'               => '0',
            ]
        );

        if ($exists === false) {
            $this->disableByDefault();
        }

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_allow_onetime',
            [
                'type'                  => 'int',
                'label'                 => 'Allow one-time purchase',
                'input'                 => 'select',
                'source'                => Boolean::class,
                'sort_order'            => 500,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => 'simple,virtual,downloadable,configurable',
                'group'                 => 'Subscription',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'used_for_promo_rules'  => false,
                'required'              => false,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_intervals',
            [
                'type'                  => 'text',
                'label'                 => 'Interval(s)',
                'input'                 => 'text',
                'backend'               => Intervals::class,
                'sort_order'            => 200,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => null,
                'group'                 => 'Subscription',
                'note'                  => 'Enter the subscription interval(s), in conjunction with unit below. EG.'
                    . ' 1 month, 90 days, etc. To give multiple options, separate the numbers by comma: 30,45,60,90',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'required'              => false,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_unit',
            [
                'type'                  => 'varchar',
                'label'                 => 'Unit',
                'input'                 => 'select',
                'source'                => Period::class,
                'sort_order'            => 300,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => null,
                'group'                 => 'Subscription',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'used_for_promo_rules'  => true,
                'required'              => false,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_length',
            [
                'type'                  => 'varchar',
                'label'                 => 'Length',
                'input'                 => 'text',
                'frontend_class'        => 'validate-number',
                'sort_order'            => 400,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => null,
                'group'                 => 'Subscription',
                'note'                  => 'Number of intervals the subscription should run. 0 for indefinitely.',
                'used_for_promo_rules'  => true,
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'required'              => false,
            ]
        );

        // These price attributes render weird. It's because the core has hardcoded CSS tied to field classes
        // (field-price et al.). ...
        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_price',
            [
                'type'                  => 'decimal',
                'label'                 => 'Installment Price',
                'input'                 => 'text',
                'frontend_class'        => 'validate-number',
                'sort_order'            => 500,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => 'simple,virtual,downloadable,configurable',
                'group'                 => 'Subscription',
                'note'                  => 'Regular price (for subscriptions only). Any lower price (regular, group,'
                    . 'tier, etc.) will override this.',
                'used_for_promo_rules'  => true,
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'required'              => false,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'subscription_init_adjustment',
            [
                'type'                  => 'decimal',
                'label'                 => 'Initial Order Adjustment Price',
                'input'                 => 'text',
                'frontend_class'        => 'validate-number',
                'sort_order'            => 600,
                'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'              => 'simple,virtual,downloadable,configurable',
                'group'                 => 'Subscription',
                'note'                  => 'Price to adjust the initial order by (for subscriptions only). '
                    . 'A positive adjustment will make the first billing cost more. '
                    . 'A negative adjustment will make it cost less (down to $0.00).',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => true,
                'required'              => false,
            ]
        );
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $categorySetup->removeAttribute($entityTypeId, 'subscription_active');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_allow_onetime');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_intervals');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_unit');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_length');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_price');
        $categorySetup->removeAttribute($entityTypeId, 'subscription_init_adjustment');

        $categorySetup->removeAttributeGroup($entityTypeId, $attributeSetId, 'Subscription');

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Set subscription_active to 0 for all existing products at install.
     *
     * @return void
     * @throws \Exception
     */
    protected function disableByDefault()
    {
        $products   = $this->productCollectionFactory->create();
        $productIds = $products->getAllIds();

        $this->productAction->updateAttributes(
            $productIds,
            [
                'subscription_active' => 0,
            ],
            0
        );
    }
}
