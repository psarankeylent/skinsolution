<?php

namespace Backend\CatalogTracking\Model;

class CatalogTracking extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Backend\CatalogTracking\Model\ResourceModel\CatalogTracking');
    }
}
?>