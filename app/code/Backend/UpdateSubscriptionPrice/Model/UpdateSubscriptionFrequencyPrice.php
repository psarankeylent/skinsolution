<?php

namespace Backend\UpdateSubscriptionPrice\Model;

class UpdateSubscriptionFrequencyPrice extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Backend\UpdateSubscriptionPrice\Model\ResourceModel\UpdateSubscriptionFrequencyPrice');
    }
}
?>