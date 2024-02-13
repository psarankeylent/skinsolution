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
 * Interface MessageInterface
 * @api
 */
interface MessageInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TICKET_ID = 'ticket_id';
    const GATEWAY_ID = 'gateway_id';
    const CONTENT = 'content';
    const TYPE = 'type';
    const AUTHOR_NAME = 'author_name';
    const AUTHOR_EMAIL = 'author_email';
    const CREATED_AT = 'created_at';
    const ATTACHMENTS = 'attachments';

    /**
     * Get message ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set message ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get ticket ID
     *
     * @return int
     */
    public function getTicketId();

    /**
     * Set ticket ID
     *
     * @param int $id
     * @return $this
     */
    public function setTicketId($id);

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
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get message type
     *
     * @return string
     */
    public function getType();

    /**
     * Set message type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName();

    /**
     * Set author name
     *
     * @param string $authorName
     * @return $this
     */
    public function setAuthorName($authorName);

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorEmail();

    /**
     * Set author email
     *
     * @param string $authorEmail
     * @return $this
     */
    public function setAuthorEmail($authorEmail);

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
     * Get attachments
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface[]
     */
    public function getAttachments();

    /**
     * Set attachments
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface[] $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\MessageExtensionInterface $extensionAttributes
    );
}
