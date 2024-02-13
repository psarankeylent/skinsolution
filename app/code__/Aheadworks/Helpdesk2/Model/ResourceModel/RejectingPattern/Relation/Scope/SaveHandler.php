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

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResource;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Relation\Scope
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
        $this->tableName = $this->resourceConnection->getTableName(RejectingPatternResource::SCOPE_TABLE);
    }

    /**
     * Save scope types
     *
     * @param RejectingPatternInterface $entity
     * @param array $arguments
     * @return object|bool
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (empty($entity->getScopeTypes())) {
            return $entity;
        }

        $this->deleteByEntity($entity->getId());
        $scopeData = [];
        foreach ($entity->getScopeTypes() as $scopeType) {
            $scopeData[] = [
                'pattern_id' => $entity->getId(),
                'scope' => $scopeType
            ];
        }
        $this->getConnection()->insertMultiple($this->tableName, $scopeData);

        return $entity;
    }

    /**
     * Remove scope types by pattern ID
     *
     * @param int $patternId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($patternId)
    {
        return $this->getConnection()->delete(
            $this->tableName,
            ['pattern_id = ?' => $patternId]
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
            $this->metadataPool->getMetadata(RejectingPatternInterface::class)->getEntityConnectionName()
        );
    }
}
