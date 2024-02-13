<?php
namespace LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('LegacySubscription\Subscriptions\Model\CustomerSubscription',
            'LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription'
        );
    }
}

?>
