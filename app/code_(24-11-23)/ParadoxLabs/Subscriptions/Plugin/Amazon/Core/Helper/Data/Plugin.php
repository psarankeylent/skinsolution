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

namespace ParadoxLabs\Subscriptions\Plugin\Amazon\Core\Helper\Data;

/**
 * Plugin Class
 */
class Plugin
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    private $helper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\QuoteManager
     */
    private $quoteManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Plugin constructor.
     *
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\Subscriptions\Model\Service\QuoteManager $quoteManager
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\Subscriptions\Model\Service\QuoteManager $quoteManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->quoteManager = $quoteManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Enforce Amazon Pay availability with subscriptions at time of checkout.
     *
     * @param \Amazon\Core\Helper\Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsPaymentButtonEnabled(\Amazon\Core\Helper\Data $subject, bool $result)
    {
        // If Amazon Pay is coming out as allowed, make sure the quote doesn't contain a subscription.
        // Amazon does not allow payment storage, so it's not compatible.

        if ($result === true && $this->helper->getIsFrontend()) {
            /** @var \Magento\Quote\Api\Data\CartInterface $quote */
            $quote = $this->checkoutSession->getQuote();

            if ($this->quoteManager->quoteContainsSubscription($quote) === true) {
                return false;
            }
        }

        return $result;
    }

    /**
     * Enforce Amazon Pay availability with subscriptions on the minicart.
     *
     * @param \Amazon\Core\Helper\Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsPayButtonAvailableInMinicart(\Amazon\Core\Helper\Data $subject, bool $result)
    {
        // If Amazon Pay is coming out as allowed, make sure the quote doesn't contain a subscription.
        // Amazon does not allow payment storage, so it's not compatible.

        if ($result === true && $this->helper->getIsFrontend()) {
            /** @var \Magento\Quote\Api\Data\CartInterface $quote */
            $quote = $this->checkoutSession->getQuote();

            if ($this->quoteManager->quoteContainsSubscription($quote) === true) {
                return false;
            }
        }

        return $result;
    }
}
