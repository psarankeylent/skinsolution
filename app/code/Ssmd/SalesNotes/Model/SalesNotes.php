<?php

namespace Ssmd\SalesNotes\Model;

class SalesNotes extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Ssmd\SalesNotes\Model\ResourceModel\SalesNotes');
    }
}
