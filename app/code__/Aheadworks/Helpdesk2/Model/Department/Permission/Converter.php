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
namespace Aheadworks\Helpdesk2\Model\Department\Permission;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterfaceFactory;

/**
 * Class Converter
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Permission
 */
class Converter
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DepartmentPermissionInterfaceFactory
     */
    private $departmentPermissionFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DepartmentPermissionInterfaceFactory $departmentPermissionFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DepartmentPermissionInterfaceFactory $departmentPermissionFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->departmentPermissionFactory = $departmentPermissionFactory;
    }

    /**
     * Convert from db array to interface data array
     *
     * @param array $permissionArray
     * @return array
     */
    public function fromDbArrayToDataArray($permissionArray)
    {
        $viewRoles = [];
        $updateRoles = [];

        foreach ($permissionArray as $permissionData) {
            switch ($permissionData['type']) {
                case DepartmentPermissionInterface::TYPE_VIEW:
                    $viewRoles[] = $permissionData['role_id'];
                    break;
                case DepartmentPermissionInterface::TYPE_UPDATE:
                    $updateRoles[] = $permissionData['role_id'];
                    break;
            }
        }

        $dataArray[DepartmentPermissionInterface::VIEW_ROLE_IDS] = $viewRoles;
        $dataArray[DepartmentPermissionInterface::UPDATE_ROLE_IDS] = $updateRoles;

        return $dataArray;
    }

    /**
     * Convert from db array to data object
     *
     * @param array $permissionArray
     * @return DepartmentPermissionInterface
     */
    public function fromDbArrayToDataObject($permissionArray)
    {
        /** @var DepartmentPermissionInterface $permissionObject */
        $permissionObject = $this->departmentPermissionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $permissionObject,
            $this->fromDbArrayToDataArray($permissionArray),
            DepartmentPermissionInterface::class
        );

        return $permissionObject;
    }
}
