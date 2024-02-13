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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Magento\Framework\App\Filesystem\DirectoryList;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\FileSystem\Reader as FilesystemReader;
use Aheadworks\Helpdesk2\Model\FileSystem\FileReader;

/**
 * Class Attachments
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class Attachments implements ModifierInterface
{
    /**
     * @var FilesystemReader
     */
    private $filesystemReader;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @param FilesystemReader $filesystemReader
     * @param FileReader $fileReader
     */
    public function __construct(
        FilesystemReader $filesystemReader,
        FileReader $fileReader
    ) {
        $this->filesystemReader = $filesystemReader;
        $this->fileReader = $fileReader;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        if ($eventData->getMessage()) {
            $attachments = $eventData->getMessage()->getAttachments();
            if (empty($attachments)) {
                return $emailMetadata;
            }
            $attachmentsToSend = [];
            foreach ($attachments as $attachment) {
                $attachmentFile = $this->filesystemReader->createFile($attachment->getFilePath());
                $attachmentToSend = [
                    'content' => $this->fileReader->read($attachmentFile, DirectoryList::MEDIA),
                    'name' => $attachment->getFileName()
                ];

                $attachmentsToSend[] = $attachmentToSend;
            }

            $emailMetadata->setAttachments($attachmentsToSend);
        }

        return $emailMetadata;
    }
}
