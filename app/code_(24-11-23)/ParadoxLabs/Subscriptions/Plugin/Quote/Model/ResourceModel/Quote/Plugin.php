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

namespace ParadoxLabs\Subscriptions\Plugin\Quote\Model\ResourceModel\Quote;

class Plugin
{
    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @return void
     */
    public function afterLoadByCustomerId(
        \Magento\Quote\Model\ResourceModel\Quote $subject,
        $result,
        $quote
    ) {
        $this->suppressTotalsCollection($quote);
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @return void
     */
    public function afterLoadActive(
        \Magento\Quote\Model\ResourceModel\Quote $subject,
        $result,
        $quote
    ) {
        $this->suppressTotalsCollection($quote);
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @return void
     */
    public function afterLoadByIdWithoutStore(
        \Magento\Quote\Model\ResourceModel\Quote $subject,
        $result,
        $quote
    ) {
        $this->suppressTotalsCollection($quote);
    }

    /**
     * On quote load, stop disabled products from being auto-deleted from subscriptions.
     *
     * @param $quote
     * @return void
     */
    protected function suppressTotalsCollection($quote): void
    {
        if ($quote instanceof \Magento\Quote\Model\Quote !== true) {
            return;
        }

        /**
         * When loading a subscription quote, suppress automatic totals collection. This prevents deleted products
         * from being automatically purged from the quote upon access.
         */
        $payment = $quote->getPayment();
        if ($payment->getAdditionalInformation('is_subscription_generated')) {
            $quote->setData('is_super_mode', true);
            $quote->setData('trigger_recollect', 0);
        }
    }
}
