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

namespace ParadoxLabs\Subscriptions\Plugin\Quote\Model\ResourceModel\Quote\Item\Collection;

use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Plugin Class
 */
class Plugin
{
    /**
     * On quote items load, stop disabled products from being auto-deleted from subscriptions.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $result
     * @return \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    public function afterLoadWithFilter(
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject,
        \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $result
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $result->getFirstItem()->getQuote();

        if ($quote instanceof \Magento\Quote\Model\Quote === false) {
            return $result;
        }

        $payment = $quote->getPayment();
        if ($payment->getAdditionalInformation('is_subscription_generated')) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($result->getItems() as $item) {
                if ($item->isDeleted() && (int)$item->getProduct()->getStatus() === Status::STATUS_DISABLED) {
                    $item->isDeleted(false);
                    $quote->setTotalsCollectedFlag(true);
                }
            }
        }

        return $result;
    }
}
