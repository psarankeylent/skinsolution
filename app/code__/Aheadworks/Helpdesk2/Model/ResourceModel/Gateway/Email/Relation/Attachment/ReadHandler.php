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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email\Relation\Attachment;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResource;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterfaceFactory;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email\Relation\Attachment
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
     * @var EmailAttachmentInterfaceFactory
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
     * @param EmailAttachmentInterfaceFactory $attachmentFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        EmailAttachmentInterfaceFactory $attachmentFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->attachmentFactory = $attachmentFactory;
        $this->tableName = $this->resourceConnection->getTableName(GatewayEmailResource::EMAIL_ATTACHMENT_TABLE_NAME);
    }

    /**
     * Read attachments
     *
     * @param EmailInterface $entity
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
     * @param int $emailId
     * @return EmailAttachmentInterface[]
     * @throws \Exception
     */
    private function getAttachments($emailId)
    {
        $objects = [];
        $attachments = $this->loadAttachmentData($emailId);
        foreach ($attachments as $attachment) {
            /** @var EmailAttachmentInterface $attachmentObject */
            $attachmentObject = $this->attachmentFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $attachmentObject,
                $attachment,
                EmailAttachmentInterface::class
            );
            $objects[] = $attachmentObject;
        }

        return $objects;
    }

    /**
     * Load attachment data
     *
     * @param int $emailId
     * @return array
     * @throws \Exception
     */
    private function loadAttachmentData($emailId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where(EmailAttachmentInterface::EMAIL_ID . ' = :email_id');

        return $connection->fetchAssoc($select, [EmailAttachmentInterface::EMAIL_ID => $emailId]);
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
            $this->metadataPool->getMetadata(EmailInterface::class)->getEntityConnectionName()
        );
    }
}
