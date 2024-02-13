<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Api\Data;
/**
 * ZeroDollarOrdersHistorySearchResultsInterface interface
 */
interface ZeroDollarOrdersHistorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get zero_dollar_orders_history list.
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface[]
     */
    public function getItems();

    /**
     * Set increment_id list.
     * @param \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

