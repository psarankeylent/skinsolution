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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Repository as StorefrontLabelRepository;

/**
 * Class Repository
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option
 */
class Repository
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var StorefrontLabelRepository
     */
    private $storefrontLabelRepository;

    /**
     * @var DepartmentOptionInterfaceFactory
     */
    private $departmentOptionFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param StorefrontLabelRepository $storefrontLabelRepository
     * @param DepartmentOptionInterfaceFactory $departmentOptionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        StorefrontLabelRepository $storefrontLabelRepository,
        DepartmentOptionInterfaceFactory $departmentOptionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->storefrontLabelRepository = $storefrontLabelRepository;
        $this->departmentOptionFactory = $departmentOptionFactory;
    }

    /**
     * Save department option data
     *
     * @param DepartmentOptionInterface[] $options
     * @param int $departmentId
     * @return bool
     * @throws \Exception
     */
    public function save($options, $departmentId)
    {
        foreach ($options as $option) {
            try {
                $option
                    ->setId(null)
                    ->setDepartmentId($departmentId);
                $this->entityManager->save($option);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not save department options.'));
            }
        }

        return true;
    }

    /**
     * Retrieve department option data by department ID
     *
     * @param int $departmentId
     * @param int $storeId
     * @return DepartmentOptionInterface[]
     * @throws \Exception
     */
    public function getByDepartmentId($departmentId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getDepartmentOptionTableName(), [DepartmentOptionInterface::ID])
            ->where(DepartmentOptionInterface::DEPARTMENT_ID . ' = :department_id')
            ->order(DepartmentOptionInterface::SORT_ORDER . ' ' . SortOrder::SORT_ASC);
        $optionIds = $connection->fetchCol($select, [DepartmentOptionInterface::DEPARTMENT_ID => $departmentId]);

        $options = [];
        foreach ($optionIds as $optionId) {
            /** @var DepartmentOptionInterface $option */
            $option = $this->departmentOptionFactory->create();
            $this->entityManager->load($option, $optionId, ['store_id' => $storeId]);
            $options[] = $option;
        }

        return $options;
    }

    /**
     * Retrieve single department option by its ID
     *
     * @param int $id
     * @param int $storeId
     * @return DepartmentOptionInterface
     * @throws \Exception
     */
    public function getById($id, $storeId)
    {
        /** @var DepartmentOptionInterface $option */
        $option = $this->departmentOptionFactory->create();
        $this->entityManager->load($option, $id, ['store_id' => $storeId]);

        return $option;
    }

    /**
     * Delete all existing department options by department ID
     *
     * @param int $departmentId
     * @return bool
     * @throws \Exception
     */
    public function deleteByDepartmentId($departmentId)
    {
        $connection = $this->getConnection();
        $optionSelect = $connection->select()
            ->from($this->getDepartmentOptionTableName(), [DepartmentOptionInterface::ID])
            ->where(DepartmentOptionInterface::DEPARTMENT_ID . ' = :department_id');
        $optionIds = $connection->fetchCol($optionSelect, [DepartmentOptionInterface::DEPARTMENT_ID => $departmentId]);

        $optionValueSelect = $connection->select()
            ->from($this->getDepartmentOptionValueTableName(), [DepartmentOptionInterface::ID])
            ->where(DepartmentOptionValueInterface::OPTION_ID . ' IN (?)', $optionIds);
        $optionValIds = $connection->fetchCol($optionValueSelect);

        $this
            ->removeStorefrontLabels($optionValIds, DepartmentOptionValueInterface::STOREFRONT_LABEL_ENTITY_TYPE)
            ->removeStorefrontLabels($optionIds, DepartmentOptionInterface::STOREFRONT_LABEL_ENTITY_TYPE);

        $connection->delete(
            $this->getDepartmentOptionTableName(),
            [DepartmentOptionInterface::DEPARTMENT_ID . ' = ?' => $departmentId]
        );

        return true;
    }

    /**
     * Remove all existing label data for specified entity ID and type
     *
     * @param array $entityIds
     * @return $this
     * @param string $storefrontLabelEntityType
     * @throws \Exception
     */
    private function removeStorefrontLabels($entityIds, $storefrontLabelEntityType)
    {
        foreach ($entityIds as $entityId) {
            $this->storefrontLabelRepository->delete($entityId, $storefrontLabelEntityType);
        }
        return $this;
    }

    /**
     * Retrieve department option table name
     *
     * @return string
     * @throws \Exception
     */
    private function getDepartmentOptionTableName()
    {
         return $this->metadataPool->getMetadata(DepartmentOptionInterface::class)->getEntityTable();
    }

    /**
     * Retrieve personal option value table name
     *
     * @return string
     * @throws \Exception
     */
    private function getDepartmentOptionValueTableName()
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
            $this->metadataPool->getMetadata(DepartmentOptionInterface::class)->getEntityConnectionName()
        );
    }
}
