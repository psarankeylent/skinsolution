<?php

namespace CustomOrders\GenerateOrders\Model;

class GenerateOrder extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CustomOrders\GenerateOrders\Model\ResourceModel\GenerateOrder');
    }
}

