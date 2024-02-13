<?php

namespace Ssmd\CustomerNotes\Model;

class CustomerNotes extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Ssmd\CustomerNotes\Model\ResourceModel\CustomerNotes');
    }
}
