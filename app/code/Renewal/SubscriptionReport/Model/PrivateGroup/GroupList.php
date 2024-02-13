<?php

namespace Renewal\SubscriptionReport\Model\PrivateGroup;

class GroupList implements \Magento\Framework\Option\ArrayInterface
{
    protected $collectionFactory;

    public function __construct(
        \Renewal\SubscriptionReport\Model\ResourceModel\SubscriptionReport\CollectionFactory $collectionFactory
    ) {

        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->group('sku');
        $collection->addFieldToFilter('sku', ['neq'=>null]);

        $options = [];
        foreach ($collection as $value) {
            $sku = $value->getSku();
            $options[] = ['label' => $sku, 'value' => $sku];
        }
        return $options;
    }
}
