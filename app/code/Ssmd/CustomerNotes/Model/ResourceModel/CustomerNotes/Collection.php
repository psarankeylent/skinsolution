<?php

namespace Ssmd\CustomerNotes\Model\ResourceModel\CustomerNotes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\CustomerNotes\Model\CustomerNotes', 'Ssmd\CustomerNotes\Model\ResourceModel\CustomerNotes');
    }

}
