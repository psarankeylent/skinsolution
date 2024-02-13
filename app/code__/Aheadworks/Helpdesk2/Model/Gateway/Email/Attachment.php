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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Magento\Framework\Model\AbstractExtensibleModel;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;

/**
 * Class Attachment
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class Attachment extends AbstractExtensibleModel implements EmailAttachmentInterface
{
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
    public function getEmailId()
    {
        return $this->getData(self::EMAIL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEmailId($emailId)
    {
        return $this->setData(self::EMAIL_ID, $emailId);
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }

    /**
     * @inheritdoc
     */
    public function getFilePath()
    {
        return $this->getData(self::FILE_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setFilePath($filePath)
    {
        return $this->setData(self::FILE_PATH, $filePath);
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
        \Aheadworks\Helpdesk2\Api\Data\AttachmentExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
