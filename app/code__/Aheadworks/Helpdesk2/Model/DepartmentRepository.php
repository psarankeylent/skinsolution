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
namespace Aheadworks\Helpdesk2\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\Store;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\DepartmentSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection as DepartmentCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\CollectionFactory as DepartmentCollectionFactory;

/**
 * Class DepartmentRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class DepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * @var DepartmentResourceModel
     */
    private $resource;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DepartmentInterfaceFactory
     */
    private $departmentFactory;

    /**
     * @var DepartmentCollectionFactory
     */
    private $departmentCollectionFactory;

    /**
     * @var DepartmentSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param DepartmentResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DepartmentInterfaceFactory $departmentFactory
     * @param DepartmentCollectionFactory $departmentCollectionFactory
     * @param DepartmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        DepartmentResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DepartmentInterfaceFactory $departmentFactory,
        DepartmentCollectionFactory $departmentCollectionFactory,
        DepartmentSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->departmentFactory = $departmentFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(DepartmentInterface $department)
    {
        try {
            $this->resource->save($department);
            $this->registry[$department->getId()] = $department;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $department;
    }

    /**
     * @inheritdoc
     */
    public function get($departmentId, $storeId = null)
    {
        if (!isset($this->registry[$departmentId])) {
            /** @var DepartmentInterface $department */
            $department = $this->departmentFactory->create();
            $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
            $this->resource->setArgumentsForEntity('store_id', $storeId);
            $this->resource->load($department, $departmentId);
            if (!$department->getId()) {
                throw NoSuchEntityException::singleField(DepartmentInterface::ID, $departmentId);
            }
            $this->registry[$departmentId] = $department;
        }

        return $this->registry[$departmentId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var DepartmentCollection $collection */
        $collection = $this->departmentCollectionFactory->create();
        $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
        $collection->setStoreId($storeId);

        $this->extensionAttributesJoinProcessor->process($collection, DepartmentInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var DepartmentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Department $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(DepartmentInterface $department)
    {
        try {
            $this->resource->delete($department);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$department->getId()])) {
            unset($this->registry[$department->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($departmentId)
    {
        return $this->delete($this->get($departmentId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Department $model
     * @return DepartmentInterface
     */
    private function getDataObject($model)
    {
        /** @var DepartmentInterface $object */
        $object = $this->departmentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            DepartmentInterface::class
        );

        return $object;
    }
}
