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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Permission;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractHandler;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Model\Department\Permission\Converter as PermissionConverter;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Permission
 */
class ReadHandler extends AbstractHandler
{
    /**
     * @var PermissionConverter
     */
    private $permissionConverter;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param PermissionConverter $permissionConverter
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        PermissionConverter $permissionConverter
    ) {
        parent::__construct($metadataPool, $resourceConnection);
        $this->permissionConverter = $permissionConverter;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($entity, $arguments)
    {
        $permissions = $this->getPermissionRelationData($entity->getId());
        $entity->setPermissions($permissions);
    }

    /**
     * Retrieve permission relation data
     *
     * @param int $entityId
     * @return DepartmentPermissionInterface
     * @throws \Exception
     */
    private function getPermissionRelationData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, ['role_id', 'type'])
            ->where('department_id = :id');
        $permissionArray = $connection->fetchAll($select, ['id' => $entityId]);

        return $this->permissionConverter->fromDbArrayToDataObject($permissionArray);
    }
}
