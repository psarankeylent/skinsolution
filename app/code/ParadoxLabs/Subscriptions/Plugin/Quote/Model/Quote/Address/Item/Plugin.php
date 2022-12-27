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

namespace ParadoxLabs\Subscriptions\Plugin\Quote\Model\Quote\Address\Item;

/**
 * Plugin Class
 */
class Plugin
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\Item $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function aroundImportQuoteItem(
        \Magento\Quote\Model\Quote\Address\Item $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $proceed($quoteItem);
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $item->getQuote();

        /**
         * On multishipping checkout, core doesn't copy original_custom_price. We need to make that happen.
         * Otherwise multishipping totals come out totally vanilla.
         */
        if ($quote instanceof \Magento\Quote\Api\Data\CartInterface
            && $quote->getIsMultiShipping()) {
            $item->setOriginalCustomPrice($quoteItem->getOriginalCustomPrice());
            $item->setCustomPrice($quoteItem->getCustomPrice());
        }

        return $item;
    }
}
