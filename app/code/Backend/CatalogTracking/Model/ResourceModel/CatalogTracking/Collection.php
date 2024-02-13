<?php

namespace Backend\CatalogTracking\Model\ResourceModel\CatalogTracking;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Backend\CatalogTracking\Model\CatalogTracking', 'Backend\CatalogTracking\Model\ResourceModel\CatalogTracking');
    }

}

?>