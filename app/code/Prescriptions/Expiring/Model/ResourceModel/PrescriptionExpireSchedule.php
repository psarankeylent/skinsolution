<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PrescriptionExpireSchedule extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('prescription_expire_schedule', 'id');
    }
}

