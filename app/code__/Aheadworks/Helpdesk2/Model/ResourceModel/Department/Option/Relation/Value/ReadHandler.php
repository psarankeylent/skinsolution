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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Relation\Value;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterfaceFactory;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Relation\Value
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DepartmentOptionValueInterfaceFactory
     */
    private $departmentOptionValueFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param DepartmentOptionValueInterfaceFactory $departmentOptionValueFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        DepartmentOptionValueInterfaceFactory $departmentOptionValueFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        $this->departmentOptionValueFactory = $departmentOptionValueFactory;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentOptionInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getId()) {
            return $entity;
        }

        $entity->setValues($this->getRelatedDataByOption($entity->getId(), $arguments['store_id']));
        return $entity;
    }

    /**
     * Retrieve option related data
     *
     * @param int $entityId
     * @param int $storeId
     * @return array
     * @throws \Exception
     */
    private function getRelatedDataByOption($entityId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTableName())
            ->where(DepartmentOptionValueInterface::OPTION_ID . ' = :option_id')
            ->order(DepartmentOptionValueInterface::SORT_ORDER . ' ' . SortOrder::SORT_ASC);
        $optionValueIds = $connection->fetchCol($select, [DepartmentOptionValueInterface::OPTION_ID => $entityId]);

        $optionValues = [];
        foreach ($optionValueIds as $optionValueId) {
            /** @var DepartmentOptionValueInterface $optionValue */
            $optionValue = $this->departmentOptionValueFactory->create();
            $this->entityManager->load($optionValue, $optionValueId, ['store_id' => $storeId]);
            $optionValues[] = $optionValue;
        }

        return $optionValues;
    }

    /**
     * Retrieve table name
     *
     * @return string
     * @throws \Exception
     */
    private function getTableName()
    {
        return $this->metadataPool->getMetadata(DepartmentOptionValueInterface::class)->getEntityTable();
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
            $this->metadataPool->getMetadata(DepartmentOptionValueInterface::class)->getEntityConnectionName()
        );
    }
}
