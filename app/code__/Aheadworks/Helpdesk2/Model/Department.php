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

use Magento\Framework\Validator\ValidatorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Department extends AbstractModel implements DepartmentInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'aw_helpdesk2_department';

    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProcessorInterface $objectDataProcessor
     * @param ValidatorInterface $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProcessorInterface $objectDataProcessor,
        ValidatorInterface $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectDataProcessor = $objectDataProcessor;
        $this->validator = $validator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(DepartmentResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getIsAllowGuest()
    {
        return $this->getData(self::IS_ALLOW_GUEST);
    }

    /**
     * @inheritdoc
     */
    public function setIsAllowGuest($isAllowGuest)
    {
        return $this->setData(self::IS_ALLOW_GUEST, $isAllowGuest);
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryAgentId()
    {
        return $this->getData(self::PRIMARY_AGENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPrimaryAgentId($primaryAgentId)
    {
        return $this->setData(self::PRIMARY_AGENT_ID, $primaryAgentId);
    }

    /**
     * @inheritdoc
     */
    public function getAgentIds()
    {
        return $this->getData(self::AGENT_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setAgentIds($agentIds)
    {
        return $this->setData(self::AGENT_IDS, $agentIds);
    }

    /**
     * @inheritdoc
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
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
    public function getPermissions()
    {
        return $this->getData(self::PERMISSIONS);
    }

    /**
     * @inheritdoc
     */
    public function setPermissions($permissions)
    {
        return $this->setData(self::PERMISSIONS, $permissions);
    }

    /**
     * @inheritdoc
     */
    public function getGatewayIds()
    {
        return $this->getData(self::GATEWAY_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setGatewayIds($gatewayIds)
    {
        return $this->setData(self::GATEWAY_IDS, $gatewayIds);
    }

    /**
     * @inheritdoc
     */
    public function getStorefrontLabels()
    {
        return $this->getData(self::STOREFRONT_LABELS);
    }

    /**
     * @inheritdoc
     */
    public function setStorefrontLabels($labels)
    {
        return $this->setData(self::STOREFRONT_LABELS, $labels);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentStorefrontLabel()
    {
        return $this->getData(self::CURRENT_STOREFRONT_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setCurrentStorefrontLabel($label)
    {
        return $this->setData(self::CURRENT_STOREFRONT_LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getStorefrontLabelEntityType()
    {
        return self::STOREFRONT_LABEL_ENTITY_TYPE;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->objectDataProcessor->prepareModelBeforeSave($this);
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
        \Aheadworks\Helpdesk2\Api\Data\DepartmentExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
