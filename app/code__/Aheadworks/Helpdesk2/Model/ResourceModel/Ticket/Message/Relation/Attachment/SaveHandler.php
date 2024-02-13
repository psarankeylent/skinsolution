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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Relation\Attachment;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResource;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterfaceFactory;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Relation\Attachment
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(MessageResource::ATTACHMENT_TABLE_NAME);
    }

    /**
     * Save attachments
     *
     * @param MessageInterface $entity
     * @param array $arguments
     * @return object|bool
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (empty($entity->getAttachments())) {
            return $entity;
        }

        $this->deleteByEntity($entity->getId());
        $attachmentData = [];
        /** @var MessageAttachmentInterface $attachment */
        foreach ($entity->getAttachments() as $attachment) {
            $attachmentData[] = [
                MessageAttachmentInterface::MESSAGE_ID => $entity->getId(),
                MessageAttachmentInterface::FILE_PATH => $attachment->getFilePath(),
                MessageAttachmentInterface::FILE_NAME => $attachment->getFileName()
            ];
        }
        $this->getConnection()->insertMultiple($this->tableName, $attachmentData);

        return $entity;
    }

    /**
     * Remove attachments by message ID
     *
     * @param int $messageId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($messageId)
    {
        return $this->getConnection()->delete(
            $this->tableName,
            [MessageAttachmentInterface::MESSAGE_ID . ' = ?' => $messageId]
        );
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(MessageInterface::class)->getEntityConnectionName()
        );
    }
}
