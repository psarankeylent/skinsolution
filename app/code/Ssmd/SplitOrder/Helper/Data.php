<?php

namespace Ssmd\SplitOrder\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check if module is active.
     *
     * @param int $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'Ssmd_split_order/module/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get attributes to split.
     *
     * @param int $storeId
     * @return string
     */
    public function getAttributes($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'Ssmd_split_order/options/attributes',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should split delivery.
     *
     * @param string $storeId
     * @return bool
     */
    public function getShippingSplit($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'Ssmd_split_order/options/shipping',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get which kind of attribute related with qty should be load.
     *
     * @param int $storeId
     * @return bool
     */
    public function getQtyType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'Ssmd_split_order/options/attribute_qty',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * If should apply out of stock if inventory is empty.
     *
     * @param int $storeId
     * @return string
     */
    public function getBackorder($storeId = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'Ssmd_split_order/options/qty_backorder',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
