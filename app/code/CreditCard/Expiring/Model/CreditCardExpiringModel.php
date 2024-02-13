<?php

namespace CreditCard\Expiring\Model;

class CreditCardExpiringModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CreditCard\Expiring\Model\ResourceModel\CreditCardExpiringModel');
    }
}
?>