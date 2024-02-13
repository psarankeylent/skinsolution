<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PrescriptionExpireScheduleRepositoryInterface
{

    /**
     * Save prescription_expire_schedule
     * @param \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
     * @return \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
    );

    /**
     * Retrieve prescription_expire_schedule
     * @param string $prescriptionExpireScheduleId
     * @return \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($prescriptionExpireScheduleId);

    /**
     * Retrieve prescription_expire_schedule matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete prescription_expire_schedule
     * @param \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
    );

    /**
     * Delete prescription_expire_schedule by ID
     * @param string $prescriptionExpireScheduleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($prescriptionExpireScheduleId);
}

