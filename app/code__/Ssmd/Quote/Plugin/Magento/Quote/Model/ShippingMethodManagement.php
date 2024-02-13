<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\Quote\Plugin\Magento\Quote\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use ParadoxLabs\Subscriptions\Model\Service\QuoteManager;

class ShippingMethodManagement
{

    const FREE_SHIPPING_LIMIT = 100;

    const FLAT_SHIPPING_LIMIT = 60;
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteManager $quoteManager

    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteManager = $quoteManager;
    }

    public function afterEstimateByAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $result,
        $cartId,
        $address
    ) {
        //Your plugin code
        $quote = $this->quoteRepository->getActive($cartId);
        $result = $this->filterShippingMethods($quote, $result);

        return $result;
    }

    public function afterEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $result,
        $cartId,
        $address
    ) {
        //Your plugin code
        $quote = $this->quoteRepository->getActive($cartId);
        $result = $this->filterShippingMethods($quote, $result);

        return $result;
    }

    public function afterEstimateByAddressId(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $result,
        $cartId,
        $addressId
    ) {
        //Your plugin code
        $quote = $this->quoteRepository->getActive($cartId);
        $result = $this->filterShippingMethods($quote, $result);

        return $result;
    }

    protected function filterShippingMethods($quote, $shippingMethods)
    {
        $subtotal = $quote->getTotals()['subtotal']['value'];

        if ($this->quoteManager->quoteContainsSubscription($quote) || $subtotal >= self::FREE_SHIPPING_LIMIT)
        {
            // include only free shipping method
            return $this->filterShippingMethod($shippingMethods, 'freeshipping_freeshipping');
        }

        if ($subtotal < self::FLAT_SHIPPING_LIMIT) {
            // include only flat rate $5 shipping method
            return $this->filterShippingMethod($shippingMethods, 'flatrate_flatrate');
        }

        // Else when quote subtotal ranges between 60 and 100, exclude free shipping and flat rate
        $excludeMethods = ['flatrate_flatrate', 'freeshipping_freeshipping'];
        return $this->excludeShippingMethods($shippingMethods, $excludeMethods);
    }

    protected function excludeShippingMethods($shippingMethods, $excludeMethods) {
        $methods = [];

        foreach ($shippingMethods as $shippingMethod) {
            $currentMethod = $shippingMethod->getCarrierCode() . '_' . $shippingMethod->getMethodCode();

            if (!in_array($currentMethod, $excludeMethods)) {
                $methods[] = $shippingMethod;
            }
        }

        return $methods;
    }

    protected function filterShippingMethod($shippingMethods, $includeMethod) {
        $method = [];
        foreach ($shippingMethods as $shippingMethod) {
            $currentMethod = $shippingMethod->getCarrierCode() . '_' . $shippingMethod->getMethodCode();
            if ($currentMethod === $includeMethod) {
                $method[] = $shippingMethod;
            }
        }
        return $method;
    }

}

