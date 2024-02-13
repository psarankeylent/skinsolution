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
 * Rejected message interface
 *
 * @api
 */
interface RejectedMessageInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TYPE = 'type';
    const FROM = 'from';
    const SUBJECT = 'subject';
    const CONTENT = 'content';
    const REJECT_PATTERN_ID = 'reject_pattern_id';
    const MESSAGE_DATA = 'message_data';
    const CREATED_AT = 'created_at';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom();

    /**
     * Set from
     *
     * @param string $from
     * @return $this
     */
    public function setFrom($from);

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
     * Get message data
     *
     * @param null $key
     * @return string
     */
    public function getMessageData($key = null);

    /**
     * Set message data
     *
     * @param string $data
     * @return $this
     */
    public function setMessageData($data);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectedMessageExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectedMessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Helpdesk2\Api\Data\RejectedMessageExtensionInterface $extensionAttributes);
}
