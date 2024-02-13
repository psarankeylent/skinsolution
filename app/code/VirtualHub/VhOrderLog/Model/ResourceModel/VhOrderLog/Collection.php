<?php

namespace VirtualHub\VhOrderLog\Model\ResourceModel\VhOrderLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('VirtualHub\VhOrderLog\Model\VhOrderLog', 'VirtualHub\VhOrderLog\Model\ResourceModel\VhOrderLog');
    }

}

?>