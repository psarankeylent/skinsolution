<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MhByOrdersRepositoryInterface
{

    /**
     * Save mh_by_orders
     * @param \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
    );

    /**
     * Retrieve mh_by_orders
     * @param string $mhByOrdersId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($mhByOrdersId);

    /**
     * Retrieve mh_by_orders matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MH\Tables\Api\Data\MhByOrdersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete mh_by_orders
     * @param \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
    );

    /**
     * Delete mh_by_orders by ID
     * @param string $mhByOrdersId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($mhByOrdersId);
}

