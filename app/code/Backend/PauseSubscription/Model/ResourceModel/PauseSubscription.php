<?php

namespace Backend\PauseSubscription\Model\ResourceModel;

class PauseSubscription extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pause_subscription', 'id');
    }
}
?>
