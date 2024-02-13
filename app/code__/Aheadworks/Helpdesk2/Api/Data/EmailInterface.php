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
 * Interface EmailInterface
 *
 * @package Aheadworks\Helpdesk2\Api\Data
 */
interface EmailInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const UID = 'uid';
    const GATEWAY_ID = 'gateway_id';
    const FROM = 'from';
    const TO = 'to';
    const CC_RECIPIENTS = 'cc_recipients';
    const STATUS = 'status';
    const SUBJECT = 'subject';
    const BODY = 'body';
    const HEADERS = 'headers';
    const CONTENT_TYPE = 'content_type';
    const REJECT_PATTERN_ID = 'reject_pattern_id';
    const CREATED_AT = 'created_at';
    const TICKET_MESSAGE_ID = 'ticket_message_id';
    const ATTACHMENTS = 'attachments';

    /**
     * Get mail ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set mail ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get mail UID
     *
     * @return string
     */
    public function getUid();

    /**
     * Set mail UID
     *
     * @param string $mailUid
     * @return $this
     */
    public function setUid($mailUid);

    /**
     * Get gateway ID
     *
     * @return int
     */
    public function getGatewayId();

    /**
     * Set gateway ID
     *
     * @param int $gatewayId
     * @return $this
     */
    public function setGatewayId($gatewayId);

    /**
     * Get mail sender
     *
     * @return string
     */
    public function getFrom();

    /**
     * Set mail sender
     *
     * @param string $from
     * @return $this
     */
    public function setFrom($from);

    /**
     * Get mail recipient
     *
     * @return string
     */
    public function getTo();

    /**
     * Set mail recipient
     *
     * @param string $to
     * @return $this
     */
    public function setTo($to);

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
     * Get mail status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set mail status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Get body
     *
     * @return string
     */
    public function getBody();

    /**
     * Set body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body);

    /**
     * Get headers
     *
     * @return string
     */
    public function getHeaders();

    /**
     * Set headers
     *
     * @param string $headers
     * @return $this
     */
    public function setHeaders($headers);

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Set content type
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * Get reject pattern ID
     *
     * @return int|null
     */
    public function getRejectPatternId();

    /**
     * Set reject pattern ID
     *
     * @param int $rejectPatternId
     * @return $this
     */
    public function setRejectPatternId($rejectPatternId);

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
     * Get ticket message id
     *
     * @return string
     */
    public function getTicketMessageId();

    /**
     * Set ticket message id
     *
     * @param int $ticketMessageId
     * @return $this
     */
    public function setTicketMessageId($ticketMessageId);

    /**
     * Get attachments
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface[]
     */
    public function getAttachments();

    /**
     * Set attachments
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface[] $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\EmailExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\EmailExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\EmailExtensionInterface $extensionAttributes
    );
}
