<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Model\Data;

use MH\Tables\Api\Data\MhByOrdersInterface;

class MhByOrders extends \Magento\Framework\Api\AbstractExtensibleObject implements MhByOrdersInterface
{

    /**
     * Get mh_by_orders_id
     * @return string|null
     */
    public function getMhByOrdersId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set mh_by_orders_id
     * @param string $mhByOrdersId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setMhByOrdersId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId()
    {
        return $this->_get(self::QUESTION_ID);
    }

    /**
     * Set question_id
     * @param string $questionId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setQuestionId($questionId)
    {
        return $this->setData(self::QUESTION_ID, $questionId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \MH\Tables\Api\Data\MhByOrdersExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \MH\Tables\Api\Data\MhByOrdersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MH\Tables\Api\Data\MhByOrdersExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get question_text
     * @return string|null
     */
    public function getQuestionText()
    {
        return $this->_get(self::QUESTION_TEXT);
    }

    /**
     * Set question_text
     * @param string $questionText
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setQuestionText($questionText)
    {
        return $this->setData(self::QUESTION_TEXT, $questionText);
    }

    /**
     * Get response
     * @return string|null
     */
    public function getResponse()
    {
        return $this->_get(self::RESPONSE);
    }

    /**
     * Set response
     * @param string $response
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setResponse($response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

