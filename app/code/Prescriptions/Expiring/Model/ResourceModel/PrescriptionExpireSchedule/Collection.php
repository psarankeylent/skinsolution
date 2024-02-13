<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Model\ResourceModel\PrescriptionExpireSchedule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    //protected $_idFieldName = 'prescription_expire_schedule_id';

    protected $_idFieldName = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Prescriptions\Expiring\Model\PrescriptionExpireSchedule::class,
            \Prescriptions\Expiring\Model\ResourceModel\PrescriptionExpireSchedule::class
        );
    }
}

