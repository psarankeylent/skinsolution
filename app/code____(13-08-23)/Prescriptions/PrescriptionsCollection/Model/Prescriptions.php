<?php

namespace Prescriptions\PrescriptionsCollection\Model;

class Prescriptions extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Prescriptions\PrescriptionsCollection\Model\ResourceModel\Prescriptions');
    }
}
?>