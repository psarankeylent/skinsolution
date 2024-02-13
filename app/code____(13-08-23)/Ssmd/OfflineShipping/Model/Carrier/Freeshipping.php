<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\OfflineShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Freeshipping extends \Magento\OfflineShipping\Model\Carrier\Freeshipping
{
    /**
     * FreeShipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_freeshipping_offline_shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message : ' . $request->getPackageValueWithDiscount());

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->_updateFreeMethodQuote($request);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteManager = $objectManager->create(\ParadoxLabs\Subscriptions\Model\Service\QuoteManager::class);

        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create(\Magento\Checkout\Model\Cart::class);
        $quote = $cart->getQuote();*/


        $items = $request->getAllItems();
        if (empty($items)) {
            return false;
        }

        /** @var \Magento\Quote\Model\Quote\Item $firstItem */
        $firstItem = reset($items);
        if (!$firstItem) {
            return false;
        }

        $quote = $firstItem->getQuote();

        $logger->info(print_r($quote->getId(), true));

        if ($request->getFreeShipping() || $request->getPackageValueWithDiscount() >= $this->getConfigData(
                'free_shipping_subtotal'
            ) || $quoteManager->quoteContainsSubscription($quote)
        ) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        } elseif ($this->getConfigData('showmethod')) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $errorMsg = $this->getConfigData('specificerrmsg');
            $error->setErrorMessage(
                $errorMsg ? $errorMsg : __(
                    'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                )
            );
            return $error;
        }
        return $result;
    }
}
