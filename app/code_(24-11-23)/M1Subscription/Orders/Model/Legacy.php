<?php

namespace M1Subscription\Orders\Model;

class Legacy extends \Magento\Framework\Model\AbstractModel
{
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('M1Subscription\Orders\Model\ResourceModel\Legacy');
    }

    
}
