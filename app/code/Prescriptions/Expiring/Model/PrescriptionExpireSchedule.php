<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Model;

use Magento\Framework\Model\AbstractModel;
use Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface;

class PrescriptionExpireSchedule extends AbstractModel implements PrescriptionExpireScheduleInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Prescriptions\Expiring\Model\ResourceModel\PrescriptionExpireSchedule::class);
    }

    /**
     * @inheritDoc
     */
    public function getPrescriptionExpireScheduleId()
    {
        return $this->getData(self::PRESCRIPTION_EXPIRE_SCHEDULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPrescriptionExpireScheduleId($prescriptionExpireScheduleId)
    {
        return $this->setData(self::PRESCRIPTION_EXPIRE_SCHEDULE_ID, $prescriptionExpireScheduleId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
}

