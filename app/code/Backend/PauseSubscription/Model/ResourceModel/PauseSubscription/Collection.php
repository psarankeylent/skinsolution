<?php

namespace Backend\PauseSubscription\Model\ResourceModel\PauseSubscription;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Backend\PauseSubscription\Model\PauseSubscription', 'Backend\PauseSubscription\Model\ResourceModel\PauseSubscription');
    }

}

?>