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

namespace ParadoxLabs\Subscriptions\Observer;

/**
 * SetProductOptionPricesObserver Class
 */
class SetProductOptionPricesObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface
     */
    protected $intervalRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * setProductOptionPricesObserver constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository
     * @param \Magento\Framework\Registry $registry
     * @param \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository,
        \Magento\Framework\Registry $registry,
        \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager $currencyManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->intervalRepository = $intervalRepository;
        $this->registry = $registry;
        $this->currencyManager = $currencyManager;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $this->registry->registry('current_product');
        $options = $observer->getData('configObj');

        // If there is no product or no options, abort.
        if ($product instanceof \Magento\Catalog\Model\Product === false
            || $options instanceof \Magento\Framework\DataObject === false
            || empty($options->getData('config'))
            || $this->config->isDynamicPricingEnabled() === false) {
            return;
        }

        $optionConfig = $options->getData('config');
        $intervals    = $this->intervalRepository->getIntervalsByProductId(
            $product->getData('row_id') ?: $product->getId()
        );
        foreach ($intervals->getItems() as $interval) {
            if (isset($optionConfig[$interval->getOptionId()][$interval->getValueId()]['prices']['finalPrice'])) {
                $optionConfig[$interval->getOptionId()][$interval->getValueId()] = $this->updateOptionPrice(
                    $product,
                    $interval,
                    $optionConfig[$interval->getOptionId()][$interval->getValueId()]
                );
            }
        }

        $options->setData('config', $optionConfig);
    }

    /**
     * Add subscription option price data to the custom option data for dynamic price updating on product view.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface $interval
     * @param array $optionPrices
     * @return array
     */
    protected function updateOptionPrice(
        \Magento\Catalog\Model\Product $product,
        \ParadoxLabs\Subscriptions\Api\Data\ProductIntervalInterface $interval,
        $optionPrices
    ) {
        /**
         * Get currencies from quote for conversion -- this will only work on frontend, but should only run there too.
         */
        $quote = $this->checkoutSession->getQuote();
        $baseCurrency = $quote->getBaseCurrencyCode() ?: $quote->getStore()->getBaseCurrencyCode();
        $quoteCurrency = $quote->getQuoteCurrencyCode() ?: $quote->getStore()->getCurrentCurrencyCode();

        /**
         * Add subscription installment price difference to the custom option, if relevant
         *
         * Note: We have to do this at runtime because core does not allow a custom option's price to be entered as a
         * negative value. This works positive or negative--just the difference from the current calculated price.
         */
        if (!empty($interval->getInstallmentPrice())) {
            $baseDifference = $interval->getInstallmentPrice() - $product->getFinalPrice();
            $difference     = $this->currencyManager->convertPriceCurrency(
                $baseDifference,
                $baseCurrency,
                $quoteCurrency
            );

            $optionPrices['prices']['oldPrice']['amount']   += $difference;
            $optionPrices['prices']['basePrice']['amount']  += $baseDifference;
            $optionPrices['prices']['finalPrice']['amount'] += $difference;
        }

        /**
         * Add subscription adjustment price to the custom option, if relevant
         *
         * Note: This looks funky, but it plays right with how core handles prices and adjustments on the product page.
         * We want the adjustment price to be calculated, but not to actually show up in the dropdown (until we add it
         * on our own as a separate part of the label). The amount is used for the selected-option-price calculation;
         * the adjustment is *added* to that, but only for the option label price, not the actual price.
         *
         * why? don't ask me.
         *
         * @see ParadoxLabs_Subscriptions::js/product/view/price-options-mixin.js
         */
        if (!empty($interval->getAdjustmentPrice())) {
            $baseAdjustment = $interval->getAdjustmentPrice();
            $adjustment     = $this->currencyManager->convertPriceCurrency(
                $baseAdjustment,
                $baseCurrency,
                $quoteCurrency
            );

            $optionPrices['prices']['oldPrice']['amount']   += $adjustment;
            $optionPrices['prices']['basePrice']['amount']  += $baseAdjustment;
            $optionPrices['prices']['finalPrice']['amount'] += $adjustment;

            $optionPrices['prices']['oldPrice']['adjustments']['subscription']   = -$adjustment;
            $optionPrices['prices']['basePrice']['adjustments']['subscription']  = -$baseAdjustment;
            $optionPrices['prices']['finalPrice']['adjustments']['subscription'] = -$adjustment;
        }

        return $optionPrices;
    }
}
