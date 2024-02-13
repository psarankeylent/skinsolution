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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Message;

use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Attachment\Loader as AttachmentLoader;
use Aheadworks\Helpdesk2\Model\FileSystem\Reader;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class DownloadAttachment
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Message
 */
class DownloadAttachment implements CommandInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var AttachmentLoader
     */
    private $attachmentLoader;

    /**
     * @var Reader
     */
    private $filesystemReader;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @param TicketManagementInterface $ticketManagement
     * @param AttachmentLoader $attachmentLoader
     * @param Reader $filesystemReader
     * @param FileFactory $fileFactory
     */
    public function __construct(
        TicketManagementInterface $ticketManagement,
        AttachmentLoader $attachmentLoader,
        Reader $filesystemReader,
        FileFactory $fileFactory
    ) {
        $this->ticketManagement = $ticketManagement;
        $this->attachmentLoader = $attachmentLoader;
        $this->filesystemReader = $filesystemReader;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($data)
    {
        if (!isset($data['attachment_id'])) {
            throw new \InvalidArgumentException('Attachment ID is required to be downloaded');
        }

        $attachment = $this->attachmentLoader->loadById($data['attachment_id']);
        $file = $this->filesystemReader->createFile($attachment->getFilePath());
        return $this->fileFactory->create($attachment->getFileName(), $file, DirectoryList::MEDIA);
    }
}
