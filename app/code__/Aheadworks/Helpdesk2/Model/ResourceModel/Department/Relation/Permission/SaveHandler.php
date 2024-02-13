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

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractSaveHandler;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Permission
 */
class SaveHandler extends AbstractSaveHandler
{
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
    public function process($entity, $arguments = [])
    {
        $this->deleteOldChildEntityById($entity->getId());
        $toInsert = $this->preparePermissionRelationData($entity);
        $this->insertChildEntity($toInsert);

        return $entity;
    }

    /**
     * Retrieve array of agent relation data to insert
     *
     * @param DepartmentInterface $entity
     * @return array
     */
    private function preparePermissionRelationData($entity)
    {
        /** @var DepartmentPermissionInterface $permissions */
        $permissions = $entity->getPermissions();
        $permissionData = [];
        if ($permissions) {
            $viewRoleIds = $this->prepareRoles($permissions->getViewRoleIds());
            $updateRoleIds = $this->prepareRoles($permissions->getUpdateRoleIds());

            foreach ($viewRoleIds as $roleId) {
                $permissionData[] = [
                    'department_id' => $entity->getId(),
                    'role_id' => $roleId,
                    'type' => DepartmentPermissionInterface::TYPE_VIEW,
                ];
            }
            foreach ($updateRoleIds as $roleId) {
                $permissionData[] = [
                    'department_id' => $entity->getId(),
                    'role_id' => $roleId,
                    'type' => DepartmentPermissionInterface::TYPE_UPDATE,
                ];
            }
        }

        return $permissionData;
    }

    /**
     * Prepare roles for save
     *
     * @param array $roles
     * @return array
     */
    private function prepareRoles($roles)
    {
        if (is_string($roles)) {
             $roles = explode(',', $roles);
        }
        if (in_array(DepartmentPermissionInterface::ALL_ROLES_ID, $roles)) {
            $roles = [DepartmentPermissionInterface::ALL_ROLES_ID];
        }

        return $roles;
    }
}
