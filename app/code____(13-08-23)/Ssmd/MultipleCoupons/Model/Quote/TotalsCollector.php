<?php

namespace Ssmd\MultipleCoupons\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total\Collector;
use Magento\Quote\Model\Quote\Address\Total\CollectorFactory;
use Magento\Quote\Model\Quote\Address\Total\CollectorInterface;

/**
 * Class TotalsCollector
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TotalsCollector extends \Magento\Quote\Model\Quote\TotalsCollector
{
    /**
     * Total models collector
     *
     * @var \Magento\Quote\Model\Quote\Address\Total\Collector
     */
    protected $totalCollector;

    /**
     * @var \Magento\Quote\Model\Quote\Address\Total\CollectorFactory
     */
    protected $totalCollectorFactory;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Model\Quote\Address\TotalFactory
     */
    protected $totalFactory;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollectorList
     */
    protected $collectorList;

    /**
     * Quote validator
     *
     * @var \Magento\Quote\Model\QuoteValidator
     */
    protected $quoteValidator;

    /**
     * @var \Magento\Quote\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Magento\Quote\Model\ShippingAssignmentFactory
     */
    protected $shippingAssignmentFactory;

    /**
     * Validate coupon code.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    protected function _validateCouponCode(\Magento\Quote\Model\Quote $quote)
    {
        $code = $quote->getData('coupon_code');
        $couponCode = '';
        if (strlen($code)) {
            $addressHasCoupon = false;
            $addresses = $quote->getAllAddresses();
            if (count($addresses) > 0) {
                foreach ($addresses as $address) {
                    if ($address->hasCouponCode()) {
                        $addressHasCoupon = true;
                        $couponCode = $address->getCouponCode();
                    }
                }
                if (!$addressHasCoupon) {
                    $quote->setCouponCode('');
                } else {
                    $quote->setCouponCode($couponCode);
                }
            }
        }
        return $this;
    }
}
