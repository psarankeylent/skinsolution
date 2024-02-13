<?php

namespace VirtualHub\VhOrderLog\Model\ResourceModel;

class VhOrderLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('vh_orders_log', 'id');
    }
}
?>
