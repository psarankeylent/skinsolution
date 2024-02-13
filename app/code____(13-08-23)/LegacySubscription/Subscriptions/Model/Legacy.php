<?php

namespace LegacySubscription\Subscriptions\Model;

class Legacy extends \Magento\Framework\Model\AbstractModel
{
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('LegacySubscription\Subscriptions\Model\ResourceModel\Legacy');
    }

    
}
