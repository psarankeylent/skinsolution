<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\Medical\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MedicalRepositoryInterface
{

    /**
     * Save Medical
     * @param \Backend\Medical\Api\Data\MedicalInterface $medical
     * @return \Backend\Medical\Api\Data\MedicalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Backend\Medical\Api\Data\MedicalInterface $medical
    );

    /**
     * Retrieve Medical
     * @param string $medicalId
     * @return \Backend\Medical\Api\Data\MedicalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($medicalId);

    /**
     * Retrieve Medical matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Backend\Medical\Api\Data\MedicalSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Medical
     * @param \Backend\Medical\Api\Data\MedicalInterface $medical
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Backend\Medical\Api\Data\MedicalInterface $medical
    );

    /**
     * Delete Medical by ID
     * @param string $medicalId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($medicalId);
}

