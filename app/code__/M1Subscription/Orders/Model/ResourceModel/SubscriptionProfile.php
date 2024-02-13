<?php

namespace M1Subscription\Orders\Model\ResourceModel;

class SubscriptionProfile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('subscription_profile', 'entity_id');
    }
}

