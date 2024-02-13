<?php

namespace Backend\UpdateSubscriptionPrice\Model\ResourceModel;

class UpdateSubscriptionFrequencyPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('paradoxlabs_subscription_product_interval_quote', 'id');
    }
}
?>
