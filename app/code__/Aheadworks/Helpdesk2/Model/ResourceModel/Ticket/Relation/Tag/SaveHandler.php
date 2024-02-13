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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\Tag;

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Api\Data\TagInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TagRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Relation\Tag
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var TagInterfaceFactory
     */
    private $tagFactory;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param TagRepositoryInterface $tagRepository
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param TagInterfaceFactory $tagFactory
     */
    public function __construct(
        TagRepositoryInterface $tagRepository,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        TagInterfaceFactory $tagFactory
    ) {
        $this->tagRepository = $tagRepository;
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tagFactory = $tagFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $tagNames = $entity->getTagNames() ? : [];

        $entityTags = [];
        $existingTags = [];
        foreach ($this->getTicketTagsData($tagNames, $entityId) as $data) {
            $tagName = $data['name'];
            if ($data['ticket_id'] == $entityId) {
                $entityTags[$data['id']] = $tagName;
            } else {
                $existingTags[$data['id']] = $tagName;
            }
        }

        $new = array_udiff($tagNames, $entityTags, 'strcasecmp');
        $toCreate = array_udiff($new, $existingTags, 'strcasecmp');
        $toInsert = array_udiff($new, $toCreate, 'strcasecmp');
        $toDelete = array_udiff($entityTags, $tagNames, 'strcasecmp');

        if ($toInsert) {
            $this->saveRelations(
                $entityId,
                array_keys(array_uintersect($existingTags, $toInsert, 'strcasecmp'))
            );
        }
        if ($toCreate) {
            $this->saveRelations($entityId, $this->createTags($toCreate));
        }
        if ($toDelete) {
            $this->getConnection()->delete(
                $this->resourceConnection->getTableName(TagResource::RELATION_TABLE_NAME),
                [
                    'ticket_id = ?' => $entityId,
                    'tag_id IN (?)' => array_keys(array_uintersect($entityTags, $toDelete, 'strcasecmp'))
                ]
            );
        }

        return $entity;
    }

    /**
     * Get tags data with given tag names or associated to a given entity Id
     *
     * @param array $tagNames
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    private function getTicketTagsData(array $tagNames, $entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['tag' => $this->resourceConnection->getTableName(TagResource::MAIN_TABLE_NAME)], ['id', 'name'])
            ->joinLeft(
                ['tag_ticket' => $this->resourceConnection->getTableName(TagResource::RELATION_TABLE_NAME)],
                'tag.id = tag_ticket.tag_id',
                ['ticket_id']
            )
            ->where('name IN(?)', $tagNames)
            ->orWhere('ticket_id = ?', $entityId);
        return $connection->fetchAll($select);
    }

    /**
     * Create tags, return IDs of created tags
     *
     * @param array $tagNames
     * @return int[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createTags(array $tagNames)
    {
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            /** @var TagInterface $tag */
            $tag = $this->tagFactory->create();
            $tag->setName($tagName);
            $this->tagRepository->save($tag);
            $tagIds[] = $tag->getId();
        }
        return $tagIds;
    }

    /**
     * Insert rows with tag IDs into tag relation table
     *
     * @param int $entityId
     * @param array $tagIds
     * @return void
     * @throws \Exception
     */
    private function saveRelations($entityId, array $tagIds)
    {
        $data = [];
        foreach ($tagIds as $tagId) {
            $data[] = [
                'tag_id' => $tagId,
                'ticket_id' => $entityId
            ];
        }
        $this->getConnection()->insertMultiple(
            $this->resourceConnection->getTableName(TagResource::RELATION_TABLE_NAME),
            $data
        );
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
        );
    }
}
