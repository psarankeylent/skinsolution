<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Api\Data;

interface PrescriptionExpireScheduleInterface
{

    const NAME = 'name';
    const PRESCRIPTION_EXPIRE_SCHEDULE_ID = 'prescription_expire_schedule_id';

    /**
     * Get prescription_expire_schedule_id
     * @return string|null
     */
    public function getPrescriptionExpireScheduleId();

    /**
     * Set prescription_expire_schedule_id
     * @param string $prescriptionExpireScheduleId
     * @return \Prescriptions\Expiring\PrescriptionExpireSchedule\Api\Data\PrescriptionExpireScheduleInterface
     */
    public function setPrescriptionExpireScheduleId($prescriptionExpireScheduleId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Prescriptions\Expiring\PrescriptionExpireSchedule\Api\Data\PrescriptionExpireScheduleInterface
     */
    public function setName($name);
}

