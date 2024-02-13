<?php

namespace LegacySubscription\Subscriptions\Model\CustomerSubscription;

use LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{    
    /**
     * @var array
     */
    protected $_loadedData;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        //return [];

        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        //$items = $this->collection->addFieldToFilter('id', 1);
       // echo "<pre>"; print_r($items->getData()); exit;
        foreach ($items as $value) {
            $this->_loadedData[$value->getData('id')] = $value->getData();
        }
        
        //echo "<pre>"; print_r($this->_loadedData); exit;

        return $this->_loadedData;
    }

}
