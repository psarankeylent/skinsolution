<?php

namespace M1Subscription\M1SubscriptionCollection\Model\ResourceModel\M1SubscriptionCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollection', 'M1Subscription\M1SubscriptionCollection\Model\ResourceModel\M1SubscriptionCollection');
    }

}

?>