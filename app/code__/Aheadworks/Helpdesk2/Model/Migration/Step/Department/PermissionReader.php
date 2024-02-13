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
namespace Aheadworks\Helpdesk2\Model\Migration\Step\Department;

use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Magento\Framework\App\ResourceConnection;

/**
 * Class PermissionReader
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step\Department
 */
class PermissionReader
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Retrieve department permissions
     *
     * @param $departmentId
     * @return array[]
     */
    public function getPermissions($departmentId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $connection->getTableName(Helpdesk1TableNames::DEPARTMENT_PERMISSION),
                ['role_id', 'type']
            )
            ->where('department_id = :id');
        $permissions = $connection->fetchAll($select, ['id' => $departmentId]);

        $viewRoles = [];
        $updateRoles = [];

        foreach ($permissions as $permissionData) {
            switch ($permissionData['type']) {
                case 1:
                    $viewRoles[] = $permissionData['role_id'];
                    break;
                case 2:
                    $updateRoles[] = $permissionData['role_id'];
                    break;
            }
        }

        return [$viewRoles, $updateRoles];
    }
}
