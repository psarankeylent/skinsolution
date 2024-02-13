<?php

namespace TrackingActivityLog\TrackAdminUser\Model\ResourceModel\AdminUserActionsLogs;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogs', 'TrackingActivityLog\TrackAdminUser\Model\ResourceModel\AdminUserActionsLogs');
    }

}
?>