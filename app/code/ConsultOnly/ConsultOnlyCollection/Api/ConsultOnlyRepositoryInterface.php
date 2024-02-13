<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ConsultOnlyRepositoryInterface
{

    /**
     * Save ConsultOnly
     * @param \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
    );

    /**
     * Retrieve ConsultOnly
     * @param string $consultonlyId
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($consultonlyId);

    /**
     * Retrieve ConsultOnly matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ConsultOnly
     * @param \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
    );

    /**
     * Delete ConsultOnly by ID
     * @param string $consultonlyId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($consultonlyId);
}

