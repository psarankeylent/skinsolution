<?php
/**
 * Copyright © 2015 Enhance. All rights reserved.
 */
namespace Ssmd\Attributes\Model\ResourceModel;

/**
 * WPPages resource
 */
class Prescriptions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('prescriptions', 'prescription_id');
    }


}
