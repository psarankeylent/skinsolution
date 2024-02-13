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

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Validator\ValidatorInterface;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Automation extends AbstractModel implements AutomationInterface
{
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
        $this->_init(AutomationResourceModel::class);
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
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * @inheritdoc
     */
    public function getIsRequiredToBreak()
    {
        return $this->getData(self::IS_REQUIRED_TO_BREAK);
    }

    /**
     * @inheritdoc
     */
    public function setIsRequiredToBreak($isRequiredToBreak)
    {
        return $this->setData(self::IS_REQUIRED_TO_BREAK, $isRequiredToBreak);
    }

    /**
     * @inheritdoc
     */
    public function getEvent()
    {
        return $this->getData(self::EVENT);
    }

    /**
     * @inheritdoc
     */
    public function setEvent($event)
    {
        return $this->setData(self::EVENT, $event);
    }

    /**
     * @inheritdoc
     */
    public function getConditions()
    {
        return $this->getData(self::CONDITIONS);
    }

    /**
     * @inheritdoc
     */
    public function setConditions($conditions)
    {
        return $this->setData(self::CONDITIONS, $conditions);
    }

    /**
     * @inheritdoc
     */
    public function getActions()
    {
        return $this->getData(self::ACTIONS);
    }

    /**
     * @inheritdoc
     */
    public function setActions($actions)
    {
        return $this->setData(self::ACTIONS, $actions);
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
        \Aheadworks\Helpdesk2\Api\Data\AutomationExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
