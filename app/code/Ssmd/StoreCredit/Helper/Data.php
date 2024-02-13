<?php

namespace Ssmd\StoreCredit\Helper;

use Ssmd\StoreCredit\Model\ResourceModel\QuoteStorecredit\CollectionFactory as QSCCollectionFactory;
use Ssmd\StoreCredit\Model\QuoteStorecreditFactory  as QSCFactory;
use Ssmd\StoreCredit\Model\StorecreditFactory as SCFactory;

class Data
{
    /**
     * @var QSCFactory
     */
    protected $qscFactory;

    /**
     * @var QSCCollectionFactory
     */
    protected $qscCollectionFactory;

    /**
     * @param QSCCollectionFactory $qscCollectionFactory
     * @param QSCFactory $qscFactory
     */
    public function __construct(
        QSCCollectionFactory $qscCollectionFactory,
        QSCFactory $qscFactory,
        SCFactory $scFactory
    ) {
        $this->qscCollectionFactory = $qscCollectionFactory;
        $this->qscFactory = $qscFactory;
        $this->scFactory = $scFactory;
    }

    public function searchByQuoteId($quoteId)
    {
        $collection = $this->qscCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId);

        if ($collection->count() > 0)
            return $collection->getItems();

        return null;
    }

    public function getQuotesStoreCredit($quoteId)
    {
        $collection = $this->searchByQuoteId($quoteId);

        if ($collection)
            return current($collection)['applied_storecredit'];

        return null;
    }

    public function removeQuotesStoreCredit($quoteId)
    {
        $collection = $this->searchByQuoteId($quoteId);

        if ($collection) {
            foreach ($collection as $item) {
                $item->delete();
            }
        }

        return null;
    }

    public function applyQuotesStoreCredit($data)
    {
        $collection = $this->searchByQuoteId($data['quote_id']);
        $customerStoreCredits = $this->getCustomerStoreCredits($data['customer_id']);

        $amount = $data['amount'] ?? 0;
        if ($customerStoreCredits) {
            if ($collection) {
                foreach ($collection as $item) {
                    $item->delete();
                }
            }

            $this->qscFactory->create()
                ->setData([
                    'quote_id' => $data['quote_id'],
                    'applied_storecredit' => $amount,
                ])
                ->save();
        }
    }

    public function logStoreCreditUsage($data)
    {
        try {
            $customerStoreCredits = $this->getCustomerStoreCredits($data['customer_id']) ?? 0;

            $totalCredits = $customerStoreCredits - (float)$data['amount'];

            $this->scFactory->create()
                ->setCustomerId($data['customer_id'])
                ->setAmount($totalCredits)
                ->setComments('Applied an amount of $' . (float)$data['amount'] . ' on Order : ' . $data['increment_id'])
                ->setCreatedAt(date('Y-m-d h:i:s'))
                ->save();

        } catch (Exception $e) {
            // Log Exception Here
        }
    }

    public function getCustomerStoreCredits($customerId)
    {
        try {
            $collection = $this->scFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('id', 'DESC');

            if ($collection->count()) {
                return (float)$collection->getFirstItem()->getData()['amount'];
            }
        } catch (Exception $e) {
            // Log Exception Here
        }

        return null;
    }
}