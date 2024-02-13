<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Api\Data;

interface MhByOrdersInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const MH_BY_ORDERS_ID = 'mh_by_orders_id';
    const UPDATED_AT = 'updated_at';
    const QUESTION_ID = 'question_id';
    const QUESTION_TEXT = 'question_text';
    const RESPONSE = 'response';
    const CUSTOMER_ID = 'customer_id';

    /**
     * Get mh_by_orders_id
     * @return string|null
     */
    public function getMhByOrdersId();

    /**
     * Set mh_by_orders_id
     * @param string $mhByOrdersId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setMhByOrdersId($mhByOrdersId);

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId();

    /**
     * Set question_id
     * @param string $questionId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setQuestionId($questionId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \MH\Tables\Api\Data\MhByOrdersExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \MH\Tables\Api\Data\MhByOrdersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MH\Tables\Api\Data\MhByOrdersExtensionInterface $extensionAttributes
    );

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get question_text
     * @return string|null
     */
    public function getQuestionText();

    /**
     * Set question_text
     * @param string $questionText
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setQuestionText($questionText);

    /**
     * Get response
     * @return string|null
     */
    public function getResponse();

    /**
     * Set response
     * @param string $response
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setResponse($response);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \MH\Tables\Api\Data\MhByOrdersInterface
     */
    public function setUpdatedAt($updatedAt);
}

