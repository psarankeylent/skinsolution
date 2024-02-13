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
namespace Aheadworks\Helpdesk2\Model\Migration\Step;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface as PermissionInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Aheadworks\Helpdesk2\Model\Migration\Step\Department\PermissionReader;
use Aheadworks\Helpdesk2\Model\Migration\Step\Department\StoreLabelsReader;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\SampleData\Installer\Department as DepartmentInstaller;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Type as GatewayType;
use Magento\Framework\App\ResourceConnection;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step
 */
class Department implements MigrationStepInterface
{
    /**
     * @var DepartmentInstaller
     */
    private $departmentInstaller;

    /**
     * @var UserCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var PermissionReader
     */
    private $permissionsReader;

    /**
     * @var StoreLabelsReader
     */
    private $storeLabelsReader;

    /**
     * @param DepartmentInstaller $departmentInstaller
     * @param UserCollectionFactory $collectionFactory
     * @param ResourceConnection $resource
     * @param PermissionReader $permissionReader
     * @param StoreLabelsReader $storeLabelsReader
     */
    public function __construct(
        DepartmentInstaller $departmentInstaller,
        UserCollectionFactory $collectionFactory,
        ResourceConnection $resource,
        PermissionReader $permissionReader,
        StoreLabelsReader $storeLabelsReader
    ) {
        $this->departmentInstaller = $departmentInstaller;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resource;
        $this->permissionsReader = $permissionReader;
        $this->storeLabelsReader = $storeLabelsReader;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function migrate($limit)
    {
        $departmentsToMigrate = $this->getDepartmentsToMigrate();
        $migratedEntityCount = 0;
        /** @var UserCollection $userCollection */
        $userCollection = $this->collectionFactory->create();
        foreach ($departmentsToMigrate as $departmentData) {
            $departmentId = $departmentData['id'];
            if (!$this->departmentInstaller->ifExists($departmentId)) {
                // gateway
                $gatewayData = $this->getGatewayData($departmentId);
                if (!empty($gatewayData)) {
                    $gatewayData[GatewayDataInterface::TYPE] = GatewayType::EMAIL;
                    $gatewayData[GatewayDataInterface::GATEWAY_PROTOCOL] =
                        strtolower($gatewayData[GatewayDataInterface::GATEWAY_PROTOCOL]);
                    $this->saveToDataBase(GatewayResourceModel::MAIN_TABLE_NAME, $gatewayData);
                }

                // department
                $departmentData[DepartmentInterface::PRIMARY_AGENT_ID] = null;
                $departmentData[DepartmentInterface::AGENT_IDS] = $userCollection->getAllIds();
                $departmentData[DepartmentInterface::OPTIONS] = [];
                $departmentData[DepartmentInterface::GATEWAY_IDS] = isset($gatewayData['id']) ? [$gatewayData['id']] : [];
                $departmentData[DepartmentInterface::STORE_IDS] = [0];

                // permissions
                list($viewRoles, $updateRoles) = $this->permissionsReader->getPermissions($departmentId);
                $departmentData[DepartmentInterface::PERMISSIONS] = [
                    PermissionInterface::VIEW_ROLE_IDS => $viewRoles,
                    PermissionInterface::UPDATE_ROLE_IDS => $updateRoles
                ];

                // store labels
                $storeFrontLabelData = [];
                $storeFrontLabelData[] = [
                    StorefrontLabelInterface::STORE_ID => 0,
                    StorefrontLabelInterface::CONTENT => $departmentData['name']
                ];
                foreach ($this->storeLabelsReader->getStoreLabels($departmentId) as $storeLabelData) {
                    $storeFrontLabelData[] = [
                        StorefrontLabelInterface::STORE_ID => $storeLabelData[StorefrontLabelInterface::STORE_ID],
                        StorefrontLabelInterface::CONTENT => $storeLabelData[StorefrontLabelInterface::CONTENT]
                    ];
                }
                $departmentData[DepartmentInterface::STOREFRONT_LABELS] = $storeFrontLabelData;

                // create department
                $this->departmentInstaller->createDepartment($departmentData);
                $migratedEntityCount++;
            }
        }

        return $migratedEntityCount . ' departments were migrated';
    }

    /**
     * Get gateway data
     *
     * @param int $departmentId
     * @return array
     */
    public function getGatewayData($departmentId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(
                ['main_table' => $this->resourceConnection->getTableName(Helpdesk1TableNames::DEPARTMENT_GATEWAY)],
                [
                    GatewayDataInterface::ID => 'main_table.id',
                    GatewayDataInterface::IS_ACTIVE => 'main_table.is_enabled',
                    GatewayDataInterface::NAME => 'CONCAT("Gateway ", main_table.id)',
                    GatewayDataInterface::DEFAULT_STORE_ID => 'main_table.default_store_id',
                    GatewayDataInterface::GATEWAY_PROTOCOL => 'main_table.protocol',
                    GatewayDataInterface::HOST => 'main_table.host',
                    GatewayDataInterface::AUTHORIZATION_TYPE => 'main_table.auth_type',
                    GatewayDataInterface::EMAIL => 'auth_tbl.email',
                    GatewayDataInterface::LOGIN => 'auth_tbl.client_id',
                    GatewayDataInterface::PASSWORD => 'auth_tbl.client_secret',
                    GatewayDataInterface::CLIENT_ID => 'auth_tbl.client_id',
                    GatewayDataInterface::SECURITY_PROTOCOL => 'main_table.secure_type',
                    GatewayDataInterface::PORT => 'main_table.port',
                    GatewayDataInterface::IS_DELETE_FROM_HOST => 'main_table.is_delete_parsed',
                ]
            )->joinInner(
                ['auth_tbl' => $this->resourceConnection->getTableName(Helpdesk1TableNames::DEPARTMENT_GATEWAY_AUTH)],
                'main_table.id = auth_tbl.gateway_id AND main_table.auth_type = auth_tbl.auth_type',
                []
            )->where('main_table.department_id = ?', $departmentId);

        return $connection->fetchRow($select);
    }

    /**
     * Get departments to migrate
     *
     * @return array[]
     */
    private function getDepartmentsToMigrate()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(
                ['main_table' => $this->resourceConnection->getTableName(Helpdesk1TableNames::DEPARTMENT)],
                [
                    DepartmentInterface::ID => 'main_table.id',
                    DepartmentInterface::NAME => 'main_table.name',
                    DepartmentInterface::IS_ACTIVE => 'main_table.is_enabled'
                ]
            );

        return $connection->fetchAll($select);
    }

    /**
     * Save data to database
     *
     * @param string $table
     * @param array $data
     */
    private function saveToDataBase($table, $data)
    {
        $this->resourceConnection
            ->getConnection()
            ->insertOnDuplicate($this->resourceConnection->getTableName($table), $data);
    }
}
