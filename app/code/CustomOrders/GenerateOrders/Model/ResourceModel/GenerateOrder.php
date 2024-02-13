<?php

namespace CustomOrders\GenerateOrders\Model\ResourceModel;

class GenerateOrder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('custom_orders', 'id');
    }
}

