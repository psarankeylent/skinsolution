<?php

namespace Backend\CatalogTracking\Model\ResourceModel;

class CatalogTracking extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tracking_catalog', 'id');
    }
}
?>
