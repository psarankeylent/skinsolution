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
namespace Aheadworks\Helpdesk2\Model\Rejection;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage as RejectedMessageResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Message
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection
 */
class Message extends AbstractModel implements RejectedMessageInterface
{
    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProcessorInterface $objectDataProcessor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->objectDataProcessor = $objectDataProcessor;
    }


    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RejectedMessageResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getID()
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
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getFrom()
    {
        return $this->getData(self::FROM);
    }

    /**
     * @inheritDoc
     */
    public function setFrom($from)
    {
        return $this->setData(self::FROM, $from);
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritdoc
     */
    public function getRejectPatternId()
    {
        return $this->getData(self::REJECT_PATTERN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRejectPatternId($rejectPatternId)
    {
        return $this->setData(self::REJECT_PATTERN_ID, $rejectPatternId);
    }

    /**
     * @inheritDoc
     */
    public function getMessageData($key = null)
    {
        $data = $this->getData(self::MESSAGE_DATA);

        return $key != null
            ? $data[$key] ?? null
            : $data;
    }

    /**
     * @inheritDoc
     */
    public function setMessageData($data)
    {
        return $this->setData(self::MESSAGE_DATA, $data);
    }


    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
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
     */
    public function afterLoad()
    {
        $this->objectDataProcessor->prepareModelAfterLoad($this);
        return $this;
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
        \Aheadworks\Helpdesk2\Api\Data\RejectedMessageExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
