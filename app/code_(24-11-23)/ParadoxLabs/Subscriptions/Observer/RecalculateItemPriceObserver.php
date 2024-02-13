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

namespace ParadoxLabs\Subscriptions\Observer;

class RecalculateItemPriceObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * RecalculateItemPriceObserver constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->itemManager = $itemManager;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getData('quote');

        if ($quote instanceof \Magento\Quote\Model\Quote === false
            || $this->config->recalculatePricing($quote->getStoreId()) === false) {
            return;
        }

        $subtotalBySubscription = [];

        /**
         * Update item prices
         */
        foreach ($quote->getAllItems() as $item) {
            /** @var \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription */
            $subscription = $item->getData('subscription');

            if ($subscription instanceof \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface === false
                || $this->itemManager->isSubscription($item) === false) {
                continue;
            }

            $newPrice = $this->itemManager->calculatePrice(
                $item,
                $subscription->getRunCount(),
                $quote->getBaseCurrencyCode(),
                $quote->getQuoteCurrencyCode()
            );

            if ($item->getPrice() != $newPrice) {
                $item->setCustomPrice($newPrice);
                $item->setOriginalCustomPrice($newPrice);
            }

            if (!isset($subtotalBySubscription[$subscription->getId()])) {
                $subtotalBySubscription[$subscription->getId()] = 0;
            }

            $subtotal = $newPrice * $item->getQty();
            if ($this->config->subtotalShouldIncludeTax($subscription->getStoreId())) {
                $subtotal *= 1 + ($item->getTaxPercent() / 100);
            }

            $subtotalBySubscription[$subscription->getId()] += $subtotal;
        }

        // Update subscription subtotal(s)
        foreach ($observer->getData('subscriptions') as $subscription) {
            if (isset($subtotalBySubscription[$subscription->getId()])) {
                $subscription->setSubtotal((float)$subtotalBySubscription[$subscription->getId()]);
            }
        }
    }
}
