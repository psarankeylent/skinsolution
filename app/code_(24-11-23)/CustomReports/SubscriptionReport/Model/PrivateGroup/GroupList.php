<?php

namespace CustomReports\SubscriptionReport\Model\PrivateGroup;

class GroupList implements \Magento\Framework\Option\ArrayInterface
{
    protected $collectionFactory;

    public function __construct(
        \CustomReports\SubscriptionReport\Model\ResourceModel\SubscriptionReport\CollectionFactory $collectionFactory
    ) {
        
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->group('sku');

        $collection->addFieldToFilter('sku', ['neq'=>null]);

       // $query = $collection->getSelect()->__toString(); 
       // echo "<pre>"; print_r($collection->getData()); exit;
        //echo $query;
        //exit;

        $options = [];
        foreach ($collection as $value) {
            $sku = $value->getSku();
            $options[] = ['label' => $sku, 'value' => $sku];
        }
        return $options;
    }
}