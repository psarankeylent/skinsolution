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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Department;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection as GatewayCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory as GatewayCollectionFactory;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Department
 */
class Gateway extends AbstractValidator
{
    /**
     * @var GatewayCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param GatewayCollectionFactory $collectionFactory
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        GatewayCollectionFactory $collectionFactory,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Check if already assigned gateway is used again
     *
     * @param DepartmentInterface $department
     * @return bool
     * @throws \Exception
     */
    public function isValid($department)
    {
        $this->_clearMessages();

        /** @var GatewayCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->applyNotAssignedFilter();

        try {
            $originalDepartment = $this->departmentRepository->get($department->getId());
            $originalGatewayIds = $originalDepartment->getGatewayIds();
        } catch (NoSuchEntityException $exception) {
            $originalGatewayIds = [];
        }
        $allIds = array_merge($collection->getAllIds(), $originalGatewayIds);

        $getawayIds = $department->getGatewayIds();

        if (!empty($getawayIds) && (!in_array(reset($getawayIds), $allIds))) {
            $this->_addMessages([__('Gateway is already assigned to other department')]);
        }

        return empty($this->getMessages());
    }
}
