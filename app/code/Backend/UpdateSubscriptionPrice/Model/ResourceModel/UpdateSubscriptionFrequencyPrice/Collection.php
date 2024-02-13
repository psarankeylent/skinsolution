<?php

namespace Backend\UpdateSubscriptionPrice\Model\ResourceModel\UpdateSubscriptionFrequencyPrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Backend\UpdateSubscriptionPrice\Model\UpdateSubscriptionFrequencyPrice', 'Backend\UpdateSubscriptionPrice\Model\ResourceModel\UpdateSubscriptionFrequencyPrice');
    }

}

?>