<?php
namespace TrackingActivityLog\TrackAdminUser\Model;

class AdminUserActionsLogs extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('TrackingActivityLog\TrackAdminUser\Model\ResourceModel\AdminUserActionsLogs');
    }
}
?>