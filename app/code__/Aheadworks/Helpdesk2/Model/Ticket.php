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
namespace Aheadworks\Helpdesk2\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Validator\ValidatorInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;

/**
 * Class Ticket
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Ticket extends AbstractModel implements TicketInterface
{
    const ENTITY = 'aw_helpdesk2_ticket';

    /**
     * @var string
     */
    protected $_eventPrefix = 'aw_helpdesk2_ticket';

    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var TicketGridResourceModel
     */
    private $ticketGridResource;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProcessorInterface $objectDataProcessor
     * @param ValidatorInterface $validator
     * @param TicketGridResourceModel $ticketGridResource
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProcessorInterface $objectDataProcessor,
        ValidatorInterface $validator,
        TicketGridResourceModel $ticketGridResource,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->objectDataProcessor = $objectDataProcessor;
        $this->validator = $validator;
        $this->ticketGridResource = $ticketGridResource;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(TicketResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * @inheritdoc
     */
    public function setRating($rating)
    {
        return $this->setData(self::RATING, $rating);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerRating()
    {
        return $this->getData(self::CUSTOMER_RATING);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerRating($rating)
    {
        return $this->setData(self::CUSTOMER_RATING, $rating);
    }

    /**
     * @inheritdoc
     */
    public function getUid()
    {
        return $this->getData(self::UID);
    }

    /**
     * @inheritdoc
     */
    public function setUid($uid)
    {
        return $this->setData(self::UID, $uid);
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * @inheritdoc
     */
    public function getDepartmentId()
    {
        return $this->getData(self::DEPARTMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setDepartmentId($departmentId)
    {
        return $this->setData(self::DEPARTMENT_ID, $departmentId);
    }

    /**
     * @inheritdoc
     */
    public function getAgentId()
    {
        return $this->getData(self::AGENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAgentId($agentId)
    {
        return $this->setData(self::AGENT_ID, $agentId);
    }

    /**
     * @inheritdoc
     */
    public function getIsLocked()
    {
        return $this->getData(self::IS_LOCKED);
    }

    /**
     * @inheritdoc
     */
    public function setIsLocked($isLocked)
    {
        return $this->setData(self::IS_LOCKED, $isLocked);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * @inheritdoc
     */
    public function getCcRecipients()
    {
        return $this->getData(self::CC_RECIPIENTS);
    }

    /**
     * @inheritdoc
     */
    public function setCcRecipients($ccRecipients)
    {
        return $this->setData(self::CC_RECIPIENTS, $ccRecipients);
    }

    /**
     * @inheritdoc
     */
    public function getStatusId()
    {
        return $this->getData(self::STATUS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStatusId($ccRecipients)
    {
        return $this->setData(self::STATUS_ID, $ccRecipients);
    }

    /**
     * @inheritdoc
     */
    public function getPriorityId()
    {
        return $this->getData(self::PRIORITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPriorityId($priorityId)
    {
        return $this->setData(self::PRIORITY_ID, $priorityId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getInternalNote()
    {
        return $this->getData(self::INTERNAL_NOTE);
    }

    /**
     * @inheritdoc
     */
    public function setInternalNote($internalNote)
    {
        return $this->setData(self::INTERNAL_NOTE, $internalNote);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getExternalLink()
    {
        return $this->getData(self::EXTERNAL_LINK);
    }

    /**
     * @inheritdoc
     */
    public function setExternalLink($externalLink)
    {
        return $this->setData(self::EXTERNAL_LINK, $externalLink);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * @inheritdoc
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @inheritdoc
     */
    public function getTagNames()
    {
        $tagNames = $this->getData(self::TAG_NAMES);

        return is_array($tagNames) ? $tagNames : [];
    }

    /**
     * @inheritdoc
     */
    public function setTagNames($tagNames)
    {
        return $this->setData(self::TAG_NAMES, $tagNames);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->isObjectNew(!$this->getId());
        $this->objectDataProcessor->prepareModelBeforeSave($this);
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterSave()
    {
        if (!$this->isObjectNew()) {
            $this->ticketGridResource->refresh($this);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterLoad()
    {
        $this->objectDataProcessor->prepareModelAfterLoad($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
