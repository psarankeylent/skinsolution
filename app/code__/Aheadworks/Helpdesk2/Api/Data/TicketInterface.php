<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketInterface
 * @api
 */
interface TicketInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
    const UID = 'uid';
    const RATING = 'rating';
    const CUSTOMER_RATING = 'customer_rating';
    const SUBJECT = 'subject';
    const DEPARTMENT_ID = 'department_id';
    const AGENT_ID = 'agent_id';
    const IS_LOCKED = 'is_locked';
    const STORE_ID = 'store_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_NAME = 'customer_name';
    const CC_RECIPIENTS = 'cc_recipients';
    const STATUS_ID = 'status_id';
    const PRIORITY_ID = 'priority_id';
    const ORDER_ID = 'order_id';
    const INTERNAL_NOTE = 'internal_note';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const EXTERNAL_LINK = 'external_link';
    const OPTIONS = 'options';
    const TAG_NAMES = 'tag_names';

    /**
     * Get ticket entity ID
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set ticket entity ID
     *
     * @param int $id
     * @return $this
     */
    public function setEntityId($id);

    /**
     * Get internal ticket unique ID
     *
     * @return string
     */
    public function getUid();

    /**
     * Set internal ticket unique ID
     *
     * @param string $uid
     * @return $this
     */
    public function setUid($uid);

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating();

    /**
     * Set rating
     *
     * @param int $rating
     * @return $this
     */
    public function setRating($rating);

    /**
     * Get customer rating
     *
     * @return int
     */
    public function getCustomerRating();

    /**
     * Set customer rating
     *
     * @param int $rating
     * @return $this
     */
    public function setCustomerRating($rating);

    /**
     * Get ticket subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set ticket subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Get department ID
     *
     * @return int
     */
    public function getDepartmentId();

    /**
     * Set department ID
     *
     * @param int $departmentId
     * @return $this
     */
    public function setDepartmentId($departmentId);

    /**
     * Get agent ID
     *
     * @return int|null
     */
    public function getAgentId();

    /**
     * Set agent ID
     *
     * @param int $agentId
     * @return $this
     */
    public function setAgentId($agentId);

    /**
     * Get is locked
     *
     * @return bool
     */
    public function getIsLocked();

    /**
     * Set is locked
     *
     * @param bool $isLocked
     * @return $this
     */
    public function setIsLocked($isLocked);

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get CC recipients
     *
     * @return string|null
     */
    public function getCcRecipients();

    /**
     * Set CC recipients
     *
     * @param string $ccRecipients
     * @return $this
     */
    public function setCcRecipients($ccRecipients);

    /**
     * Get status ID
     *
     * @return int
     */
    public function getStatusId();

    /**
     * Set status ID
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId);

    /**
     * Get priority ID
     *
     * @return int
     */
    public function getPriorityId();

    /**
     * Set priority ID
     *
     * @param int $priorityId
     * @return $this
     */
    public function setPriorityId($priorityId);

    /**
     * Get order ID
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get internal note
     *
     * @return string|null
     */
    public function getInternalNote();

    /**
     * Set internal note
     *
     * @param string $internalNote
     * @return $this
     */
    public function setInternalNote($internalNote);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get external link
     *
     * @return string
     */
    public function getExternalLink();

    /**
     * Set external link
     *
     * @param string $externalLink
     * @return $this
     */
    public function setExternalLink($externalLink);

    /**
     * Get ticket options
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface[]
     */
    public function getOptions();

    /**
     * Set ticket options
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options);

    /**
     * Get ticket tags
     *
     * @return string[]
     */
    public function getTagNames();

    /**
     * Set ticket tags
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface[] $tagNames
     * @return $this
     */
    public function setTagNames($tagNames);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketExtensionInterface $extensionAttributes
    );
}
