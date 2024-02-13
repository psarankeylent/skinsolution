<?php

namespace M1Subscription\M1SubscriptionCollection\Model;

class M1SubscriptionCollection extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('M1Subscription\M1SubscriptionCollection\Model\ResourceModel\M1SubscriptionCollection');
    }
}
?>