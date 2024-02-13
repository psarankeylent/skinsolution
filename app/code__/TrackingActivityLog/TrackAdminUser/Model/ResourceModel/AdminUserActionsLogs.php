<?php
namespace TrackingActivityLog\TrackAdminUser\Model\ResourceModel;

class AdminUserActionsLogs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('admin_user_actions_log', 'id');
    }

    
}
?>
