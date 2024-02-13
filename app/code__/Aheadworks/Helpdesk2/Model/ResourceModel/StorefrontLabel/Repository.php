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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelEntityInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;

/**
 * Class Repository
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel
 */
class Repository
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_label';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

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
    }

    /**
     * Save storefront labels
     *
     * @param StorefrontLabelEntityInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function save($entity)
    {
        if (!(int)$entity->getId()) {
            return false;
        }

        $this->deleteByEntity($entity);
        $currentLabelsData = $this->getCurrentLabelsData($entity);
        $this->insertCurrentLabelsData($currentLabelsData);

        return true;
    }

    /**
     * Retrieve storefront labels for specified entity
     *
     * @param StorefrontLabelEntityInterface $entity
     * @return array
     * @throws \Exception
     */
    public function get($entity)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(self::MAIN_TABLE_NAME))
            ->where('entity_id = :entity_id')
            ->where('entity_type = :entity_type')
            ->order('store_id ' . SortOrder::SORT_ASC);
        $labelsData = $connection->fetchAll(
            $select,
            [
                'entity_id' => $entity->getId(),
                'entity_type' => $entity->getStorefrontLabelEntityType()
            ]
        );
        return $labelsData;
    }

    /**
     * Delete all existing storefront labels for specified entity ID and type
     *
     * @param int $id
     * @param string $storefrontLabelEntityType
     * @return bool
     * @throws \Exception
     */
    public function delete($id, $storefrontLabelEntityType)
    {
        $this->getConnection()->delete(
            $this->getTableName(),
            [
                'entity_id = ?' => $id,
                'entity_type = ?' => $storefrontLabelEntityType
            ]
        );
        return true;
    }

    /**
     * Delete all existing storefront labels for specified entity
     *
     * @param StorefrontLabelEntityInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function deleteByEntity($entity)
    {
        return $this->delete($entity->getId(), $entity->getStorefrontLabelEntityType());
    }

    /**
     * Retrieve array of current storefront labels to insert
     *
     * @param StorefrontLabelEntityInterface $entity
     * @return array
     */
    private function getCurrentLabelsData($entity)
    {
        $currentLabelsData = [];
        /** @var StorefrontLabelInterface $labelsRecord */
        foreach ($entity->getStorefrontLabels() as $label) {
            $currentLabelsData[] = [
                'entity_id' => $entity->getId(),
                'entity_type' => $entity->getStorefrontLabelEntityType(),
                'store_id' => $label->getStoreId(),
                'content' => $label->getContent(),
            ];
        }

        return $currentLabelsData;
    }

    /**
     * Insert current storefront labels data
     *
     * @param array $labelsRecordsToInsert
     * @return $this
     * @throws \Exception
     */
    private function insertCurrentLabelsData($labelsRecordsToInsert)
    {
        if (!empty($labelsRecordsToInsert)) {
            $this->getConnection()->insertMultiple($this->getTableName(), $labelsRecordsToInsert);
        }
        return $this;
    }

    /**
     * Retrieve table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTableName()
    {
        return $this->metadataPool->getMetadata(StorefrontLabelInterface::class)->getEntityTable();
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
            $this->metadataPool->getMetadata(StorefrontLabelInterface::class)->getEntityConnectionName()
        );
    }
}
