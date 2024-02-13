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
namespace Aheadworks\Helpdesk2\Model\Ticket\Message;

use Magento\Framework\Model\AbstractExtensibleModel;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;

/**
 * Class Attachment
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message
 */
class Attachment extends AbstractExtensibleModel implements MessageAttachmentInterface
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
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
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
