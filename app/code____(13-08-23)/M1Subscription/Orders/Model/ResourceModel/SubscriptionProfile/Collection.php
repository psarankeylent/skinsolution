<?php

namespace M1Subscription\Orders\Model\ResourceModel\SubscriptionProfile;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('M1Subscription\Orders\Model\SubscriptionProfile', 'M1Subscription\Orders\Model\ResourceModel\SubscriptionProfile');
    }

}

?>