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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Relation\Scope;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResource;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Relation\Scope
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
     * @var string
     */
    private $tableName;

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
        $this->tableName = $this->resourceConnection->getTableName(RejectingPatternResource::SCOPE_TABLE);
    }

    /**
     * Read scope types
     *
     * @param RejectingPatternInterface $entity
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

        $scopeTypes = $this->getScopeTypes($entity->getId());
        $entity->setScopeTypes($scopeTypes);

        return $entity;
    }

    /**
     * Retrieve scope types
     *
     * @param int $patternId
     * @return string[]
     * @throws \Exception
     */
    private function getScopeTypes($patternId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, 'scope')
            ->where('pattern_id = :pattern_id');

        return $connection->fetchCol($select, ['pattern_id' => $patternId]);
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
            $this->metadataPool->getMetadata(RejectingPatternInterface::class)->getEntityConnectionName()
        );
    }
}
