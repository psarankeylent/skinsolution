<?php

namespace CreditCard\Expiring\Model\ResourceModel\CreditCardExpiringModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('CreditCard\Expiring\Model\CreditCardExpiringModel', 'CreditCard\Expiring\Model\ResourceModel\CreditCardExpiringModel');
    }

}

?>