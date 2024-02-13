<?php
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

namespace ParadoxLabs\Subscriptions\Model\Service;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface;
use ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface;

/**
 * Item Manager: Run checks and fetch subscription values relative to a particular quote/order item.
 */
class ItemManager
{
    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfig;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface
     */
    protected $intervalRepository;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $dataObjectFactory;

    /**
     * @var array
     */
    protected $intervalsByProductId = [];

    /**
     * @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[]
     */
    protected $intervalsByItemId = [];

    /**
     * Item constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper *Proxy
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig *Proxy
     * @param \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository *Proxy
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository,
        \Magento\Framework\DataObject\Factory $dataObjectFactory
    ) {
        $this->helper = $helper;
        $this->productConfig = $productConfig;
        $this->currencyManager = $currencyManager;
        $this->config = $config;
        $this->intervalRepository = $intervalRepository;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get the subscription interval (if any) for the current item. 0 for none.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getFrequencyCount(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Check for subscription.
        if ($item->getData('subscription') instanceof SubscriptionInterface) {
            return $item->getData('subscription')->getFrequencyCount();
        }

        // Check for single-option case.
        if ($this->isSingleOptionSubscription($item)) {
            return (int)$item->getProduct()->getData('subscription_intervals');
        }

        // Check for chosen interval model.
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface) {
            return $interval->getFrequencyCount();
        }

        // Fall back to legacy detection. This probably isn't necessary anymore, but safe is good.
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            $options = $this->productConfig->getCustomOptions($item);
        } else {
            $options = $item->getProductOptions();
            $options = $options['options'] ?? [];
        }

        if (is_array($options)) {
            foreach ($options as $option) {
                if (__($option['label']) === $this->config->getSubscriptionLabel()) {
                    return $this->helper->getIntervalFromString(
                        $option['value'],
                        (string)__('Every %1', $this->getFrequencyUnit($item))
                    );
                }
            }
        }

        // Check if we've reviewed this product before and pass along the known frequency. Replaced $checkedParent.
        if ($item->getData('checked_frequency') === true) {
            return $item->getData('subscription_frequency_count');
        }

        // Check for parent/child subscription item -- we want all to return the same for promo validation.
        if ($item->getParentItem() instanceof \Magento\Framework\Model\AbstractExtensibleModel) {
            $item->setData('checked_frequency', true); // Checked this product.
            $item->setData('subscription_frequency_count', $this->getFrequencyCount($item->getParentItem()));
            return $item->getData('subscription_frequency_count');
        }

        if (is_array($item->getChildren()) && !empty($item->getChildren())) {
            foreach ($item->getChildren() as $child) {
                $childCount = $this->getFrequencyCount($child);
                if ($childCount > 0) {
                    return $childCount;
                }
            }
        }

        return 0;
    }

    /**
     * Get the subscription unit for the current item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return string
     */
    public function getFrequencyUnit(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Check for subscription.
        if ($item->getData('subscription') instanceof SubscriptionInterface) {
            return $item->getData('subscription')->getFrequencyUnit();
        }

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getFrequencyUnit() !== null) {
            return $interval->getFrequencyUnit();
        }

        // Fall back to product attribute value
        if (!empty($item->getProduct()->getData('subscription_unit'))) {
            return $item->getProduct()->getData('subscription_unit');
        }

        // Check if we've reviewed this product before and pass along the known frequency. Replaced $checkedParent.
        if ($item->getData('checked_unit') === true) {
            return $item->getData('subscription_frequency_unit');
        }

        // Check for parent/child subscription item -- we want all to return the same for promo validation.
        if ($item->getParentItem() instanceof \Magento\Framework\Model\AbstractExtensibleModel) {
            $item->setData('checked_unit', true); // Checked this product.
            $item->setData('subscription_frequency_unit', $this->getFrequencyUnit($item->getParentItem()));
            return $item->getData('subscription_frequency_unit');
        }

        if (is_array($item->getChildren()) && !empty($item->getChildren())) {
            foreach ($item->getChildren() as $child) {
                $childUnit = $this->getFrequencyUnit($child);
                if (!empty($childUnit)) {
                    return $childUnit;
                }
            }
        }

        return '';
    }

    /**
     * Get the subscription length for the current item--number of billing cycles to be run. 0 for indefinite.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getLength(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getLength() !== null) {
            return $interval->getLength();
        }

        // Fall back to product attribute value
        if (!empty($item->getProduct()->getData('subscription_length'))) {
            return (int)$item->getProduct()->getData('subscription_length');
        }

        // Check if we've reviewed this product before and pass along the known frequency. Replaced $checkedParent.
        if ($item->getData('checked_length') === true) {
            return $item->getData('subscription_frequency_length');
        }

        // Check for parent/child subscription item -- we want all to return the same for promo validation.
        if ($item->getParentItem() instanceof \Magento\Framework\Model\AbstractExtensibleModel) {
            $item->setData('checked_length', true); // Checked this product.
            $item->setData('subscription_frequency_length', $this->getLength($item->getParentItem()));
            return $item->getData('subscription_frequency_length');
        }

        if (is_array($item->getChildren()) && !empty($item->getChildren())) {
            foreach ($item->getChildren() as $child) {
                $childLength = $this->getLength($child);
                if (!empty($childLength)) {
                    return $childLength;
                }
            }
        }

        return 0;
    }

    /**
     * Get the subscription adjustment price for the given item. Raw value, not converted.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return float
     */
    public function getAdjustmentPrice(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getAdjustmentPrice() !== null) {
            return $interval->getAdjustmentPrice();
        }

        // Fall back to product attribute value
        $adjustmentPrice = $item->getProduct()->getData('subscription_init_adjustment');
        if (is_numeric($adjustmentPrice)) {
            return (float)$adjustmentPrice;
        }

        return null;
    }

    /**
     * Get the subscription installment price for the given item. Raw value, not converted.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return float|null
     */
    public function getInstallmentPrice(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Return interval value if any
        $interval = $this->getIntervalModel($item);

        if ($interval instanceof ProductIntervalInterface && $interval->getInstallmentPrice() !== null) {
            return $interval->getInstallmentPrice();
        }

        // Fall back to product attribute value
        $installmentPrice = $item->getProduct()->getData('subscription_price');
        if (is_numeric($installmentPrice)) {
            return (float)$installmentPrice;
        }

        return null;
    }

    /**
     * Calculate price for a subscription item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @param int $installment
     * @param string $baseCurrency Website base currency code (convert from)
     * @param string $quoteCurrency Cart currency code (convert to)
     * @return float
     */
    public function calculatePrice(
        \Magento\Framework\Model\AbstractExtensibleModel $item,
        $installment,
        $baseCurrency = null,
        $quoteCurrency = null
    ) {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        $product = $item->getProduct();

        if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            // Due to the nature of fixed priced bundled products, product may be 0.00. Pull price from the item.
            $productPrice = $item->getPrice();
        } elseif ($product->getFinalPrice() != $product->getPrice()) {
            // Get product calculated price, minus any custom option pricing, to avoid duplication.
            $baseCustomOptionPrice = $this->getItemOptionsValue($product->getPrice(), $item);
            $productPrice = $product->getFinalPrice($item->getQty()) - $baseCustomOptionPrice;
        } else {
            $productPrice = $product->getPrice();
        }

        $price     = $productPrice;
        $basePrice = $productPrice;

        // Take subscription price to start (if any); otherwise, use normal product price.
        $installmentPrice = $this->getInstallmentPrice($item);
        if ($installmentPrice !== null) {
            $basePrice = $installmentPrice;
            $price = $this->currencyManager->convertPriceCurrency(
                $installmentPrice,
                $baseCurrency,
                $quoteCurrency
            );
        }

        // If this is the first billing, add the initial adjustment fee (if any).
        $adjustmentPrice = $this->getAdjustmentPrice($item);
        if ($installment === 1 && $adjustmentPrice !== null) {
            $basePrice += $adjustmentPrice;
            $price += $this->currencyManager->convertPriceCurrency(
                $adjustmentPrice,
                $baseCurrency,
                $quoteCurrency
            );
        }

        // Account for custom option pricing
        $configuredPrice = $this->getItemOptionsValue($basePrice, $item);
        if ($configuredPrice > 0) {
            $price += $this->currencyManager->convertPriceCurrency(
                $configuredPrice,
                $baseCurrency,
                $quoteCurrency
            );
        }

        if ($installmentPrice === null && $adjustmentPrice === null && $configuredPrice === 0.0) {
            return $product->getFinalPrice($item->getQty());
        }

        // Return calculated value. Final price must not be negative.
        return max($price, 0.0);
    }

    /**
     * Get the subscription description for the given item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return string
     */
    public function getSubscriptionDescription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        return $item->getName();
    }

    /**
     * Get the subscription run count of the given item (if any).
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return int
     */
    public function getSubscriptionRunCount(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        if ($item->getData('subscription') instanceof SubscriptionInterface) {
            // Returning one higher than recorded subscription count because most logic will occur based on the billing
            // that has yet to occur. EG, for initial purchase, run_count=0 but it's installment #1. Likewise for others
            return $item->getData('subscription')->getRunCount() + 1;
        }

        if ($this->isSubscription($item)) {
            // If item has no subscription model, must be initial order (no subscription yet), or else something else
            // unexpected happened. Default to 1 (initial billing).
            return 1;
        }

        // Check if we've reviewed this product before and pass along the known frequency. Replaced $checkedParent.
        if ($item->getData('checked_run') === true) {
            return $item->getData('subscription_frequency_run');
        }

        // Check for parent/child subscription item -- we want all to return the same for promo validation.
        if ($item->getParentItem() instanceof \Magento\Framework\Model\AbstractExtensibleModel) {
            $item->setData('checked_run', true); // Checked this product.
            $item->setData('subscription_frequency_run', $this->getSubscriptionRunCount($item->getParentItem()));
            return $item->getData('subscription_frequency_run');
        }

        if (is_array($item->getChildren()) && !empty($item->getChildren())) {
            foreach ($item->getChildren() as $child) {
                $childCount = $this->getSubscriptionRunCount($child);
                if ($childCount > 0) {
                    return $childCount;
                }
            }
        }

        // Otherwise, we've got a non-subscription item, zeroth installment.
        return 0;
    }

    /**
     * Get the ProductInterval for the given item (if any).
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    public function getIntervalModel(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */

        // Key is to prevent any chance of collision between order and quote items since this can be used for both.
        // Using object hash rather than a known ID because $item often isn't saved yet (no unique identifier).
        $itemKey = spl_object_hash($item);

        // Handle possible content staging row ID.
        if ($item->getProduct() instanceof \Magento\Catalog\Api\Data\ProductInterface
            && $item->getProduct()->hasData('row_id')) {
            $productId = $item->getProduct()->getData('row_id');
        } else {
            $productId = $item->getProductId();
        }

        /**
         * If we've already found the appropriate interval model, return it straight up.
         */
        if (isset($this->intervalsByItemId[ $itemKey ])) {
            return $this->intervalsByItemId[ $itemKey ];
        }

        /**
         * If we haven't loaded intervals for the given item's product, load them.
         */
        if (!isset($this->intervalsByProductId[ $productId ])) {
            $results = $this->intervalRepository->getIntervalsByProductId(
                $productId
            );

            $this->intervalsByProductId[ $productId ] = $results->getItems();
        }

        /**
         * If we have intervals, dig into the item options and try to make a match by option and value ID.
         */
        if (!empty($this->intervalsByProductId[ $productId ])) {
            /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */
            $intervals = $this->intervalsByProductId[ $productId ];

            if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                $interval = $this->findQuoteItemIntervalModel($item, $intervals);
            } else {
                $interval = $this->findOrderItemIntervalModel($item, $intervals);
            }

            if ($interval !== null) {
                // That's a match! Store and return it.
                $this->intervalsByItemId[ $itemKey ] = $interval;

                return $interval;
            }
        }

        // No results? Let's not do that again.
        $this->intervalsByItemId[ $itemKey ] = null;

        return null;
    }

    /**
     * Update subscription frequency/pricing based on the given option, if valid and changed
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $optionValueId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateInterval(
        SubscriptionInterface $subscription,
        \Magento\Quote\Model\Quote\Item $item,
        int $optionValueId
    ) {
        // Load and validate data
        $currentInterval = $this->getIntervalModel($item);

        if ($currentInterval instanceof \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface
            && $currentInterval->getValueId() === $optionValueId
            && $subscription->getFrequencyCount() === $currentInterval->getFrequencyCount()
            && $subscription->getFrequencyUnit() === $currentInterval->getFrequencyUnit()) {
            return;
        }

        if ($item->getProduct() instanceof \Magento\Catalog\Api\Data\ProductInterface
            && $item->getProduct()->hasData('row_id')) {
            $productId = $item->getProduct()->getData('row_id');
        } else {
            $productId = $item->getProductId();
        }

        if (!isset($this->intervalsByProductId[ $productId ])) {
            return;
        }

        /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface $interval */
        foreach ($this->intervalsByProductId[ $productId ] as $interval) {
            if ($interval->getValueId() === $optionValueId) {
                $newInterval = $interval;
                break;
            }
        }

        if (!isset($newInterval)) {
            return;
        }

        // Update frequency
        $subscription->setFrequencyCount($newInterval->getFrequencyCount());
        $subscription->setFrequencyUnit($newInterval->getFrequencyUnit());

        // Update item option
        $option = $item->getOptionByCode('option_' . $newInterval->getOptionId());
        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option === false) {
            return;
        }

        $option->setValue($newInterval->getValueId());

        $this->resetItemsCache();

        // Update price and subtotal
        $newPrice = $this->calculatePrice(
            $item,
            $subscription->getRunCount() + 1,
            $item->getQuote()->getBaseCurrencyCode(),
            $item->getQuote()->getQuoteCurrencyCode()
        );

        $item->setOriginalCustomPrice($newPrice);
        $item->setCustomPrice($newPrice);

        $subtotal = $newPrice * $item->getQty();
        if ($this->config->subtotalShouldIncludeTax($subscription->getStoreId())) {
            $subtotal *= 1 + ($item->getTaxPercent() / 100);
        }

        $subscription->setSubtotal($subtotal);
    }

    /**
     * Check whether the given item should be a subscription.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return bool
     */
    public function isSubscription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        /**
         * Check for enabled subscription and a chosen interval
         */
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        if ($item->getProduct() instanceof \Magento\Catalog\Api\Data\ProductInterface
            && $this->getFrequencyCount($item) > 0
            && !$this->useParentInfo($item)
            && (int)$item->getProduct()->getData('subscription_active') === 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the item's product has only one subscription option.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return bool
     */
    public function isSingleOptionSubscription(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        // By definition (as we currently use it), this is a product with 'onetime' disabled and one (and only one)
        // number in the 'subscription_intervals' attribute. There may or may not be a custom option and interval model.

        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        if ($item->getProduct() instanceof \Magento\Catalog\Api\Data\ProductInterface
            && (int)$item->getProduct()->getData('subscription_allow_onetime') === 0
            && !empty($item->getProduct()->getData('subscription_intervals'))
            && strpos($item->getProduct()->getData('subscription_intervals'), ',') === false) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the given item has a parent we should set the price on.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return bool
     */
    public function parentItemShouldHavePrice(\Magento\Quote\Api\Data\CartItemInterface $quoteItem)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        return $quoteItem->getParentItem() instanceof \Magento\Quote\Api\Data\CartItemInterface
            && $quoteItem->getParentItem()->getProductType() === Configurable::TYPE_CODE;
    }

    /**
     * Find an interval (if any) matching the given item.
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param $intervals
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    protected function findQuoteItemIntervalModel(\Magento\Quote\Model\Quote\Item $item, $intervals)
    {
        /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */

        $optionIds = $item->getOptionByCode('option_ids');

        if ($optionIds instanceof \Magento\Quote\Model\Quote\Item\Option) {
            // For each option and each interval, compare IDs.
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getOptionByCode('option_' . $optionId);

                if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
                    foreach ($intervals as $interval) {
                        if ($interval->getOptionId() === (int)$optionId
                            && $interval->getValueId() === (int)$option->getValue()) {
                            // That's a match!
                            return $interval;
                        }
                    }

                    $subscription = $item->getData('subscription');
                    if ($subscription instanceof SubscriptionInterface) {
                        foreach ($intervals as $interval) {
                            if ($interval->getFrequencyCount() == $subscription->getFrequencyCount()
                                && $interval->getFrequencyUnit() === $subscription->getFrequencyUnit()
                                && $interval->getLength() == $subscription->getLength()) {
                                // That's a match! Update the item option.
                                $option->setValue($interval->getValueId());
                                return $interval;
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find an interval (if any) matching the given item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @param $intervals
     * @return \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface|null
     */
    protected function findOrderItemIntervalModel(\Magento\Framework\Model\AbstractExtensibleModel $item, $intervals)
    {
        /** @var \Magento\Sales\Model\Order\Item $item */
        /** @var \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface[] $intervals */

        // For order items: options[].option_value is option_type_id, option_id is option_id.
        $options = $item->getProductOptions();
        $options = isset($options['options']) ? $options['options'] : [];

        if (is_array($options)) {
            // For each option and each interval, compare IDs.
            foreach ($options as $k => $option) {
                foreach ($intervals as $interval) {
                    $valueId = isset($option['option_value']) ? $option['option_value'] : $option['value'];

                    if (is_numeric($valueId)
                        && $interval->getOptionId() === (int)$option['option_id']
                        && $interval->getValueId() === (int)$valueId) {
                        // That's a match!
                        return $interval;
                    }
                }

                $subscription = $item->getData('subscription');
                if ($subscription instanceof SubscriptionInterface) {
                    foreach ($intervals as $interval) {
                        if ($interval->getFrequencyCount() == $subscription->getFrequencyCount()
                            && $interval->getFrequencyUnit() === $subscription->getFrequencyUnit()
                            && $interval->getLength() == $subscription->getLength()) {
                            // That's a match! Update the item option.
                            $rawOptions = $item->getProductOptions();
                            $rawOptions['options'][ $k ]['option_value'] = $interval->getValueId();
                            $item->setProductOptions($rawOptions);
                            return $interval;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get buyRequest object for the given order item.
     *
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return \Magento\Framework\DataObject
     */
    public function getInfoBuyRequest(\Magento\Framework\Model\AbstractExtensibleModel $item)
    {
        $info = $item->getProductOptionByCode('info_buyRequest');

        /**
         * Fix core webapi handling of date-type custom options -- assumes string input as of 2.3.0. cf. 1812005
         * @see \Magento\Catalog\Model\Webapi\Product\Option\Type\Date::formatValues()
         */
        if (isset($info['options'])) {
            foreach ($info['options'] as $k => $v) {
                if (is_array($v) && isset($v['year'], $v['month'], $v['day'])) {
                    if (isset($v['date_internal'])) {
                        // If we have a preparsed date string, put it back as the option.
                        $info['options'][ $k ] = $v['date_internal'];
                    } elseif (!empty($v['year']) && !empty($v['month']) && !empty($v['day'])) {
                        // Otherwise make it date-y ourselves.
                        $info['options'][ $k ] = date(
                            \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT,
                            strtotime($v['year'] . '-' . $v['month'] . '-' . $v['day'])
                        );
                    } else {
                        // Otherwise, if all values are empty, leave it with nothing to fail to parse.
                        unset($info['options'][ $k ]);
                    }
                }
            }
        }

        $info = $this->dataObjectFactory->create($info);
        $info->setData('qty', $item->getQtyOrdered());

        return $info;
    }

    /**
     * Get value of configured options.
     *
     * This is a replica of \Magento\Catalog\Pricing\Price\ConfiguredOptions for backwards compatibility to 2.1,
     * which does not have any such class nor any easy alternative. If/when 2.1 support is dropped, switch to that.
     *
     * @param float $basePrice
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return float
     * @see \Magento\Catalog\Pricing\Price\ConfiguredOptions::getItemOptionsValue()
     */
    protected function getItemOptionsValue(
        $basePrice,
        \Magento\Framework\Model\AbstractExtensibleModel $item
    ) {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        $product = $item->getProduct();
        $value = 0.0;
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds instanceof \Magento\Quote\Model\Quote\Item\Option) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option !== null) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    /** @var $group \Magento\Catalog\Model\Product\Option\Type\DefaultType */
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);
                    $value += $group->getOptionPrice($itemOption->getValue(), $basePrice);
                }
            }
        }

        return $value;
    }

    /**
     * @param \Magento\Framework\Model\AbstractExtensibleModel $item
     * @return bool
     */
    public function useParentInfo(\Magento\Framework\Model\AbstractExtensibleModel $item): bool
    {
        /** @var \Magento\Sales\Model\Order\Item|\Magento\Quote\Model\Quote\Item $item */
        $parentItem = $item->getParentItem();
        if ($parentItem instanceof \Magento\Framework\Model\AbstractExtensibleModel
            && (int)$parentItem->getProduct()->getData('subscription_active') === 1) {
            return true;
        }

        return false;
    }

    /**
     * Reset the intervals-by-item cache, forcing reload by the current item data.
     *
     * @return void
     */
    public function resetItemsCache(): void
    {
        $this->intervalsByItemId = [];
    }
}
