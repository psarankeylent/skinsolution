<?php

namespace M1Subscription\Orders\Model;

class SubscriptionProfile extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('M1Subscription\Orders\Model\ResourceModel\SubscriptionProfile');
    }
}
?>