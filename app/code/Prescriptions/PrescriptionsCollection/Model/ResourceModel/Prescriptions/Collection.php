<?php

namespace Prescriptions\PrescriptionsCollection\Model\ResourceModel\Prescriptions;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Prescriptions\PrescriptionsCollection\Model\Prescriptions', 'Prescriptions\PrescriptionsCollection\Model\ResourceModel\Prescriptions');
    }

}

?>