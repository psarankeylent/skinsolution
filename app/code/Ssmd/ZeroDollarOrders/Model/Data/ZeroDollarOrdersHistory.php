<?php
/**
 * Copyright © Zero Dollar Orders All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Model\Data;

use Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface;

class ZeroDollarOrdersHistory extends \Magento\Framework\Api\AbstractExtensibleObject implements ZeroDollarOrdersHistoryInterface
{

    /**
     * Get history_id
     * @return string|null
     */
    public function getHistoryId()
    {
        return $this->_get(self::HISTORY_ID);
    }

    /**
     * Set history_id
     * @param string $historyId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(self::HISTORY_ID, $historyId);
    }

    /**
     * Get increment_id
     * @return string|null
     */
    public function getIncrementId()
    {
        return $this->_get(self::INCREMENT_ID);
    }

    /**
     * Set increment_id
     * @param string $incrementId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order_id
     * @param string $orderId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get admin_id
     * @return string|null
     */
    public function getAdminId()
    {
        return $this->_get(self::ADMIN_ID);
    }

    /**
     * Set admin_id
     * @param string $adminId
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setAdminId($adminId)
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    /**
     * Get create_at
     * @return string|null
     */
    public function getCreateAt()
    {
        return $this->_get(self::CREATE_AT);
    }

    /**
     * Set create_at
     * @param string $createAt
     * @return \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface
     */
    public function setCreateAt($createAt)
    {
        return $this->setData(self::CREATE_AT, $createAt);
    }
}

