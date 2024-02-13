<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Api\Data;
/**
 * ZeroDollarOrdersHistoryInterface interface
 */
interface ZeroDollarOrdersHistoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const HISTORY_ID = 'history_id';
    const ORDER_ID = 'order_id';
    const CREATE_AT = 'create_at';
    const INCREMENT_ID = 'increment_id';
    const ADMIN_ID = 'admin_id';
    const CUSTOMER_ID = 'customer_id';

    /**
     * Get history_id
     * @return string|null
     */
    public function getHistoryId();

    /**
     * Set history_id
     * @param string $historyid
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setHistoryId($historyid);

    /**
     * Get increment_id
     * @return string|null
     */
    public function getIncrementId();

    /**
     * Set increment_id
     * @param string $incrementId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface $extensionAttributes
    );

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setOrderId($orderId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get admin_id
     * @return string|null
     */
    public function getAdminId();

    /**
     * Set admin_id
     * @param string $adminId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setAdminId($adminId);

    /**
     * Get create_at
     * @return string|null
     */
    public function getCreateAt();

    /**
     * Set create_at
     * @param string $createAt
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setCreateAt($createAt);
}

