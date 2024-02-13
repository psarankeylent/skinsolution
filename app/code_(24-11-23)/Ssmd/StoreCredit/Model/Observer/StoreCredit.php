<?php
namespace Ssmd\StoreCredit\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Ssmd\StoreCredit\Model\ResourceModel\QuoteStorecredit\CollectionFactory as QSCCollectionFactory;
use Ssmd\StoreCredit\Model\QuoteStorecreditFactory  as QSCFactory;
use Ssmd\StoreCredit\Helper\Data as StoreCreditHelper;
/**
 *
 */
class StoreCredit implements ObserverInterface
{
    public function __construct(
        StoreCreditHelper $storeCreditHelper
    ) {
        $this->storeCreditHelper = $storeCreditHelper;
    }

    /**
     * Reports orders placed to the database reporting_orders table
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();

        $appliedStoreCredit = $this->storeCreditHelper->getQuotesStoreCredit($quoteId);
        if ($appliedStoreCredit) {
            $data = ['increment_id' => $order->getIncrementId(), 'customer_id' => $order->getCustomerId(), 'amount' => $appliedStoreCredit];

            $this->storeCreditHelper->logStoreCreditUsage($data);
        }

    }
}
