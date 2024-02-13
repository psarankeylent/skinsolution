<?php

namespace Backend\PauseSubscription\Model;

class PauseSubscription extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Backend\PauseSubscription\Model\ResourceModel\PauseSubscription');
    }
}
?>