<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
/**
 * ZeroDollarOrdersHistoryRepositoryInterface interface
 */
interface ZeroDollarOrdersHistoryRepositoryInterface
{

    /**
     * Save zero_dollar_orders_history
     * @param \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
    );

    /**
     * Retrieve zero_dollar_orders_history
     * @param string $zeroDollarOrdersHistoryId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($zeroDollarOrdersHistoryId);

    /**
     * Retrieve zero_dollar_orders_history matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete zero_dollar_orders_history
     * @param \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
    );

    /**
     * Delete zero_dollar_orders_history by ID
     * @param string $zeroDollarOrdersHistoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($zeroDollarOrdersHistoryId);
}

