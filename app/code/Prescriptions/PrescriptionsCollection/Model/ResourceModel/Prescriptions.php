<?php

namespace Prescriptions\PrescriptionsCollection\Model\ResourceModel;

class Prescriptions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('prescriptions', 'id');
    }
}
?>
