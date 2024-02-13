<?php
namespace LegacySubscription\Subscriptions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Legacy extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('legacy_subscription', 'id');
    }
}

?>
