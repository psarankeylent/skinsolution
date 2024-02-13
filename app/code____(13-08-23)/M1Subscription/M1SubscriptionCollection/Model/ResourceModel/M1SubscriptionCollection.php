<?php

namespace M1Subscription\M1SubscriptionCollection\Model\ResourceModel;

class M1SubscriptionCollection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
?>
