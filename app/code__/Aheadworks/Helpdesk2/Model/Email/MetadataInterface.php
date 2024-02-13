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
namespace Aheadworks\Helpdesk2\Model\Email;

use Aheadworks\Helpdesk2\Model\Email\Mail\Header\Header;

/**
 * Interface MetadataInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Email
 */
interface MetadataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TEMPLATE_ID = 'template_id';
    const TEMPLATE_OPTIONS = 'template_options';
    const TEMPLATE_VARIABLES = 'template_variables';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    const CC_RECIPIENTS = 'cc_recipients';
    const EMAIL_REPLY_TO = 'email_reply_to';
    const ATTACHMENTS = 'attachments';
    const HEADERS = 'headers';
    /**#@-*/

    /**
     * Get template ID
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Set template ID
     *
     * @param string $templateId
     * @return $this
     */
    public function setTemplateId($templateId);

    /**
     * Get template options
     *
     * @return array
     */
    public function getTemplateOptions();

    /**
     * Set template options
     *
     * @param array $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions);

    /**
     * Get template variables
     *
     * @return array
     */
    public function getTemplateVariables();

    /**
     * Set template variables
     *
     * @param array $templateVariables
     * @return $this
     */
    public function setTemplateVariables($templateVariables);

    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName();

    /**
     * Set sender name
     *
     * @param string $senderName
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail();

    /**
     * Set sender email
     *
     * @param string $senderEmail
     * @return $this
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set recipient name
     *
     * @param string $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName);

    /**
     * Get recipient email
     *
     * @return string|array
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string|array $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get cc recipients
     *
     * @return array|null
     */
    public function getCcRecipients();

    /**
     * Set cc recipients
     *
     * @param array $ccRecipients
     * @return $this
     */
    public function setCcRecipients($ccRecipients);

    /**
     * Get email reply to
     *
     * @return string|null
     */
    public function getEmailReplyTo();

    /**
     * Set email reply to
     *
     * @param string $emailReplyTo
     * @return $this
     */
    public function setEmailReplyTo($emailReplyTo);

    /**
     * Get attachments
     *
     * @return array
     */
    public function getAttachments();

    /**
     * Set attachment
     *
     * @param array $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * Get headers
     *
     * @return Header[]
     */
    public function getHeaders();

    /**
     * Set headers
     *
     * @param Header[] $headers
     * @return $this
     */
    public function setHeaders($headers);

    /**
     * Add header
     *
     * @param Header $header
     * @return $this
     */
    public function addHeader($header);
}
