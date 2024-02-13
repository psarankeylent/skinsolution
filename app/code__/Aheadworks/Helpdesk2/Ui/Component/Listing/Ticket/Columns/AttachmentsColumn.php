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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\FileSystem\FileInfo;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class AttachmentsColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class AttachmentsColumn extends Column
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FileInfo $fileInfo
     * @param TicketRepositoryInterface $ticketRepository
     * @param UrlBuilder $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FileInfo $fileInfo,
        TicketRepositoryInterface $ticketRepository,
        UrlBuilder $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->fileInfo = $fileInfo;
        $this->ticketRepository = $ticketRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        $isBackend = (bool)$this->getData('config/isBackend');
        foreach ($dataSource['data']['items'] as &$item) {
            $attachments = $item[$fieldName];
            $item[$fieldName] = [];
            foreach ($attachments as $attachment) {
                if (!$this->fileInfo->isFileExist($attachment[MessageAttachmentInterface::FILE_PATH])) {
                    continue;
                }
                $attachment['file_url'] = $isBackend
                    ? $this->prepareBackendFileDownloadUrl($attachment[MessageAttachmentInterface::ID])
                    : $this->prepareFrontendFileDownloadUrl(
                        $item[MessageInterface::TICKET_ID],
                        $attachment[MessageAttachmentInterface::ID]
                    );
                $fileLength = $this->fileInfo->getFileSize($attachment[MessageAttachmentInterface::FILE_PATH]);
                $attachment['file_length'] = $this->fileInfo->formatFileSize($fileLength);
                $item[$fieldName][] = $attachment;
            }
        }

        return $dataSource;
    }

    /**
     * Prepare file download url for frontend
     *
     * @param int $ticketId
     * @param int $attachmentId
     * @return string
     * @throws NoSuchEntityException
     */
    public function prepareFrontendFileDownloadUrl($ticketId, $attachmentId)
    {
        $ticket = $this->ticketRepository->getById($ticketId);
        return $this->urlBuilder->getFrontendDownloadAttachmentLink($ticket->getExternalLink(), $attachmentId);
    }

    /**
     * Prepare file download url for backend
     *
     * @param int $attachmentId
     * @return string
     */
    public function prepareBackendFileDownloadUrl($attachmentId)
    {
        return $this->urlBuilder->getBackendDownloadAttachmentLink($attachmentId);
    }
}
