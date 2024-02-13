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
class StoreCreditChecks implements ObserverInterface
{
    public function __construct(
        StoreCreditHelper $storeCreditHelper
    ) {
        $this->storeCreditHelper = $storeCreditHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $customerId = $order->getCustomerId();

        $appliedStoreCredit = $this->storeCreditHelper->getQuotesStoreCredit($quoteId);
        if ($appliedStoreCredit) {
            $totalStoreCredits = $this->storeCreditHelper->getCustomerStoreCredits($customerId) ?? 0;

            if ($totalStoreCredits < $appliedStoreCredit) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Insufficient Store Credits. Please update your Store Credits.'));
            }
        }
    }
}
