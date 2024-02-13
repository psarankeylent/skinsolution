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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface UserProfileInterface
 * @api
 */
interface UserProfileInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const USER_ID = 'user_id';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';
    const DISPLAY_NAME = 'display_name';
    const EMAIL = 'email';
    const PHONE_NUMBER = 'phone_number';
    const IMAGE = 'image';
    const ADDITIONAL_INFORMATION = 'additional_information';
    /**#@-*/

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set User ID
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get display name
     *
     * @return string|null
     */
    public function getDisplayName();

    /**
     * Set display name
     *
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get phone number
     *
     * @return string|null
     */
    public function getPhoneNumber();

    /**
     * Set phone number
     *
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get additional information
     *
     * @return string|null
     */
    public function getAdditionalInformation();

    /**
     * Set additional information
     *
     * @param string $additionalInformation
     * @return $this
     */
    public function setAdditionalInformation($additionalInformation);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Bup\Api\Data\UserProfileExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Bup\Api\Data\UserProfileExtensionInterface $extensionAttributes
    );
}