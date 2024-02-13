<?php
namespace LegacySubscription\Subscriptions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerSubscription extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ssmd_web_customer_subscriptions', 'id');
    }
}

?>
