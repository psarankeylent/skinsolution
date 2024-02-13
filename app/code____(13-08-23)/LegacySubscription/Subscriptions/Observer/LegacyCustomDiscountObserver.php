<?php

namespace LegacySubscription\Subscriptions\Observer;

class LegacyCustomDiscountObserver implements \Magento\Framework\Event\ObserverInterface
{
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        //echo "<pre>"; print_r($quote->getData()); exit;

        $quote->setDiscountAmount('25');

        //echo "<pre>"; print_r($quote->getData()); exit;


        //$total = $observer->getEvent()->getTotal();
        
        return $this;
    }

    
}
