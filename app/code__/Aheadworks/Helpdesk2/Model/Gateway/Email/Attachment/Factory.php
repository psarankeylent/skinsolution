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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterfaceFactory;
use Aheadworks\Helpdesk2\Model\FileSystem\Writer as FileSystemWriter;
use Aheadworks\Helpdesk2\Model\FileSystem\FileUploader;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment
 */
class Factory
{
    /**
     * @var EmailAttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @param EmailAttachmentInterfaceFactory $attachmentFactory
     * @param FileSystemWriter $fileSystemWriter
     * @param FileUploader $fileUploader
     */
    public function __construct(
        EmailAttachmentInterfaceFactory $attachmentFactory,
        FileSystemWriter $fileSystemWriter,
        FileUploader $fileUploader
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Create attachment
     *
     * @param array $attachmentData
     * @return EmailAttachmentInterface
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function create($attachmentData)
    {
        $tmpFile = $this->fileSystemWriter->saveToTemporaryFile($attachmentData);
        $result = $this->fileUploader->upload($tmpFile);
        if (isset($result['error'])) {
            throw new LocalizedException(__('Error while saving attachment: %1', $result['error']));
        }

        /** @var EmailAttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $attachment
            ->setFilePath($result['file'])
            ->setFileName($result['name']);

        return $attachment;
    }
}
