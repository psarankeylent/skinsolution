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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;

/**
 * Class AbstractHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation
 */
abstract class AbstractHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        /**
         * Please override this one instead of overriding real __construct constructor
         */
        $this->_construct();
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentInterface $entity
     * @param array $arguments
     * @return object|bool
     */
    public function execute($entity, $arguments = [])
    {
        if ($this->isValid($entity)) {
            $this->process($entity, $arguments);
        }

        return $entity;
    }

    /**
     * Resource initialization
     */
    abstract protected function _construct();

    /**
     * Process object
     *
     * @param DepartmentInterface $entity
     * @param array $arguments
     * @return $this
     */
    abstract protected function process($entity, $arguments);

    /**
     * Check if valid entity
     *
     * @param DepartmentInterface $entity
     * @return bool
     */
    protected function isValid($entity)
    {
        return !empty($entity->getId());
    }

    /**
     * Initialize table
     *
     * @param string $tableName
     * @return $this
     */
    protected function initTable($tableName)
    {
        $this->tableName = $this->resourceConnection->getTableName($tableName);
        return $this;
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    protected function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(DepartmentInterface::class)->getEntityConnectionName()
        );
    }
}
