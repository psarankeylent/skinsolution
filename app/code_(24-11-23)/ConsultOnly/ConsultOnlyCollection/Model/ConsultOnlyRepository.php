<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Model;

use ConsultOnly\ConsultOnlyCollection\Api\ConsultOnlyRepositoryInterface;
use ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterfaceFactory;
use ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlySearchResultsInterfaceFactory;
use ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly as ResourceConsultOnly;
use ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly\CollectionFactory as ConsultOnlyCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class ConsultOnlyRepository implements ConsultOnlyRepositoryInterface
{

    protected $resource;

    protected $consultOnlyFactory;

    protected $consultOnlyCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataConsultOnlyFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceConsultOnly $resource
     * @param ConsultOnlyFactory $consultOnlyFactory
     * @param ConsultOnlyInterfaceFactory $dataConsultOnlyFactory
     * @param ConsultOnlyCollectionFactory $consultOnlyCollectionFactory
     * @param ConsultOnlySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceConsultOnly $resource,
        ConsultOnlyFactory $consultOnlyFactory,
        ConsultOnlyInterfaceFactory $dataConsultOnlyFactory,
        ConsultOnlyCollectionFactory $consultOnlyCollectionFactory,
        ConsultOnlySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->consultOnlyFactory = $consultOnlyFactory;
        $this->consultOnlyCollectionFactory = $consultOnlyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataConsultOnlyFactory = $dataConsultOnlyFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
    ) {
        /* if (empty($consultOnly->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $consultOnly->setStoreId($storeId);
        } */
        
        $consultOnlyData = $this->extensibleDataObjectConverter->toNestedArray(
            $consultOnly,
            [],
            \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface::class
        );
        
        $consultOnlyModel = $this->consultOnlyFactory->create()->setData($consultOnlyData);
        
        try {
            $this->resource->save($consultOnlyModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the consultOnly: %1',
                $exception->getMessage()
            ));
        }
        return $consultOnlyModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($consultOnlyId)
    {
        $consultOnly = $this->consultOnlyFactory->create();
        $this->resource->load($consultOnly, $consultOnlyId);
        if (!$consultOnly->getId()) {
            throw new NoSuchEntityException(__('ConsultOnly with id "%1" does not exist.', $consultOnlyId));
        }
        return $consultOnly->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->consultOnlyCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface $consultOnly
    ) {
        try {
            $consultOnlyModel = $this->consultOnlyFactory->create();
            $this->resource->load($consultOnlyModel, $consultOnly->getConsultonlyId());
            $this->resource->delete($consultOnlyModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ConsultOnly: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($consultOnlyId)
    {
        return $this->delete($this->get($consultOnlyId));
    }
}

