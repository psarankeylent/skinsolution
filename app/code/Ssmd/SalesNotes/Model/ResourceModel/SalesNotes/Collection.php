<?php

namespace Ssmd\SalesNotes\Model\ResourceModel\SalesNotes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\SalesNotes\Model\SalesNotes', 'Ssmd\SalesNotes\Model\ResourceModel\SalesNotes');
    }

}
