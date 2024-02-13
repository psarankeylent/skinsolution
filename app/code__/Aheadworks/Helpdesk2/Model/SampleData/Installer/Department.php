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
namespace Aheadworks\Helpdesk2\Model\SampleData\Installer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData\Installer
 */
class Department implements SampleDataInstallerInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DepartmentInterfaceFactory
     */
    private $departmentFactory;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectHelper $dataObjectHelper
     * @param DepartmentInterfaceFactory $departmentFactory
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectHelper $dataObjectHelper,
        DepartmentInterfaceFactory $departmentFactory,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->departmentFactory = $departmentFactory;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function install()
    {
        if (!$this->ifExists(1)) {
            $departmentData = [
                DepartmentInterface::ID => 1,
                DepartmentInterface::NAME => 'General department',
                DepartmentInterface::IS_ACTIVE => 1,
                DepartmentInterface::PRIMARY_AGENT_ID => null,
                DepartmentInterface::AGENT_IDS => [],
                DepartmentInterface::OPTIONS => [],
                DepartmentInterface::GATEWAY_IDS => [],
                DepartmentInterface::PERMISSIONS => [
                    DepartmentPermissionInterface::VIEW_ROLE_IDS => [0],
                    DepartmentPermissionInterface::UPDATE_ROLE_IDS => [0]
                ],
                DepartmentInterface::STOREFRONT_LABELS => [
                    [
                        StorefrontLabelInterface::STORE_ID => 0,
                        StorefrontLabelInterface::CONTENT => 'General department'
                    ]
                ],
                DepartmentInterface::STORE_IDS => [0]
            ];
            $this->createDepartment($departmentData);
        }
    }

    /**
     * Check if default department already exists
     *
     * @param int $departmentId
     * @return bool
     * @throws LocalizedException
     */
    public function ifExists($departmentId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(DepartmentInterface::ID, $departmentId)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $departmentList = $this->departmentRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($departmentList) > 0;
    }

    /**
     * Create default department
     *
     * @param array $departmentData
     * @throws LocalizedException
     */
    public function createDepartment($departmentData)
    {
        /** @var DepartmentInterface $department */
        $department = $this->departmentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $department,
            $departmentData,
            DepartmentInterface::class
        );

        $this->departmentRepository->save($department);
    }
}
