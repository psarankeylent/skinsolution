<?php

namespace CustomOrders\GenerateOrders\Model\ResourceModel\GenerateOrder;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    //protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \CustomOrders\GenerateOrders\Model\GenerateOrder::class,
            \CustomOrders\GenerateOrders\Model\ResourceModel\GenerateOrder::class
        );
    }
}

