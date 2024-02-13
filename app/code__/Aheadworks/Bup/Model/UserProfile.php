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
namespace Aheadworks\Bup\Model;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileExtensionInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class UserProfile
 *
 * @package Aheadworks\Bup\Model
 */
class UserProfile extends AbstractModel implements UserProfileInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(UserProfileResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
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
    public function getDisplayName()
    {
        return $this->getData(self::DISPLAY_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setDisplayName($displayName)
    {
        return $this->setData(self::DISPLAY_NAME, $displayName);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getPhoneNumber()
    {
        return $this->getData(self::PHONE_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setPhoneNumber($phoneNumber)
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * @inheritdoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalInformation()
    {
        return $this->getData(self::ADDITIONAL_INFORMATION);
    }

    /**
     * @inheritdoc
     */
    public function setAdditionalInformation($additionalInformation)
    {
        return $this->setData(self::ADDITIONAL_INFORMATION, $additionalInformation);
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
        UserProfileExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
