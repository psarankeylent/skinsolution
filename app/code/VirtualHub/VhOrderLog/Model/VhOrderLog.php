<?php

namespace VirtualHub\VhOrderLog\Model;

class VhOrderLog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('VirtualHub\VhOrderLog\Model\ResourceModel\VhOrderLog');
    }
}
?>