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
namespace Aheadworks\Helpdesk2\Model\Gateway;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailGatewayResourceModel;

/**
 * Class Email
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway
 */
class Email extends AbstractModel implements EmailInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(EmailGatewayResourceModel::class);
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
    public function getUid()
    {
        return $this->getData(self::UID);
    }

    /**
     * @inheritdoc
     */
    public function setUid($mailUid)
    {
        return $this->setData(self::UID, $mailUid);
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
    public function getFrom()
    {
        return $this->getData(self::FROM);
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        return $this->setData(self::FROM, $from);
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->getData(self::TO);
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        return $this->setData(self::TO, $to);
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
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
    public function getBody()
    {
        return $this->getData(self::BODY);
    }

    /**
     * @inheritdoc
     */
    public function setBody($body)
    {
        return $this->setData(self::BODY, $body);
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->getData(self::HEADERS);
    }

    /**
     * @inheritdoc
     */
    public function setHeaders($headers)
    {
        return $this->setData(self::HEADERS, $headers);
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->getData(self::CONTENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setContentType($contentType)
    {
        return $this->setData(self::CONTENT_TYPE, $contentType);
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
    public function getTicketMessageId()
    {
        return $this->getData(self::TICKET_MESSAGE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTicketMessageId($ticketMessageId)
    {
        return $this->setData(self::TICKET_MESSAGE_ID, $ticketMessageId);
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
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\EmailExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
