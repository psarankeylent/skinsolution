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

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResource;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterfaceFactory;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Relation\Attachment
 */
class ReadHandler implements ExtensionInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var MessageAttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param MessageAttachmentInterfaceFactory $attachmentFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        MessageAttachmentInterfaceFactory $attachmentFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->attachmentFactory = $attachmentFactory;
        $this->tableName = $this->resourceConnection->getTableName(MessageResource::ATTACHMENT_TABLE_NAME);
    }

    /**
     * Read attachments
     *
     * @param MessageInterface $entity
     * @param array $arguments
     * @return object|bool
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $attachments = $this->getAttachments($entity->getId());
        $entity->setAttachments($attachments);

        return $entity;
    }

    /**
     * Retrieve attachment objects
     *
     * @param int $messageId
     * @return MessageAttachmentInterface[]
     * @throws \Exception
     */
    private function getAttachments($messageId)
    {
        $objects = [];
        $attachments = $this->loadAttachmentData($messageId);
        foreach ($attachments as $attachment) {
            /** @var MessageAttachmentInterface $attachmentObject */
            $attachmentObject = $this->attachmentFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $attachmentObject,
                $attachment,
                MessageAttachmentInterface::class
            );
            $objects[] = $attachmentObject;
        }

        return $objects;
    }

    /**
     * Load attachment data
     *
     * @param int $messageId
     * @return array
     * @throws \Exception
     */
    private function loadAttachmentData($messageId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where(MessageAttachmentInterface::MESSAGE_ID . ' = :message_id');

        return $connection->fetchAssoc($select, [MessageAttachmentInterface::MESSAGE_ID => $messageId]);
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
