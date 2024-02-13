<?php

namespace Ssmd\StoreCredit\Model\ResourceModel\StorecreditOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * StoreCredit Resource Model Collection
 *
 * @author      StoreCredit
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ssmd\StoreCredit\Model\StorecreditOrder', 'Ssmd\StoreCredit\Model\ResourceModel\StorecreditOrder');
    }
}