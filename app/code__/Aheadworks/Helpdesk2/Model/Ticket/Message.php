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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;

/**
 * Class Message
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Message extends AbstractModel implements MessageInterface
{
    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @var TicketGridResourceModel
     */
    private $ticketGridResource;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProcessorInterface $objectDataProcessor
     * @param TicketGridResourceModel $ticketGridResource
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProcessorInterface $objectDataProcessor,
        TicketGridResourceModel $ticketGridResource,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->objectDataProcessor = $objectDataProcessor;
        $this->ticketGridResource = $ticketGridResource;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(MessageResourceModel::class);
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
    public function getTicketId()
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * @inheritdoc
     */
    public function getGatewayId()
    {
        return $this->getData(self::GATEWAY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setGatewayId($gatewayId)
    {
        return $this->setData(self::GATEWAY_ID, $gatewayId);
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
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
     * @inheritdoc
     */
    public function getAuthorName()
    {
        return $this->getData(self::AUTHOR_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setAuthorName($authorName)
    {
        return $this->setData(self::AUTHOR_NAME, $authorName);
    }

    /**
     * @inheritdoc
     */
    public function getAuthorEmail()
    {
        return $this->getData(self::AUTHOR_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setAuthorEmail($authorEmail)
    {
        return $this->setData(self::AUTHOR_EMAIL, $authorEmail);
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
    public function getAttachments()
    {
        return $this->getData(self::ATTACHMENTS);
    }

    /**
     * @inheritdoc
     */
    public function setAttachments($attachments)
    {
        return $this->setData(self::ATTACHMENTS, $attachments);
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
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterSave()
    {
        $this->ticketGridResource->refresh($this->getTicketId());
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
        \Aheadworks\Helpdesk2\Api\Data\MessageExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
