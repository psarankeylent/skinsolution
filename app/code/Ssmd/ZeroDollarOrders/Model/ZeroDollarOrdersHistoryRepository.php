<?php
/**
 * Copyright © Zero Dollar Orders All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterfaceFactory;
use Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistorySearchResultsInterfaceFactory;
use Ssmd\ZeroDollarOrders\Api\ZeroDollarOrdersHistoryRepositoryInterface;
use Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory as ResourceZeroDollarOrdersHistory;
use Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory\CollectionFactory as ZeroDollarOrdersHistoryCollectionFactory;

class ZeroDollarOrdersHistoryRepository implements ZeroDollarOrdersHistoryRepositoryInterface
{

    protected $zeroDollarOrdersHistoryCollectionFactory;

    private $collectionProcessor;

    protected $dataObjectHelper;

    protected $resource;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $zeroDollarOrdersHistoryFactory;

    private $storeManager;

    protected $extensibleDataObjectConverter;
    protected $dataZeroDollarOrdersHistoryFactory;


    /**
     * @param ResourceZeroDollarOrdersHistory $resource
     * @param ZeroDollarOrdersHistoryFactory $zeroDollarOrdersHistoryFactory
     * @param ZeroDollarOrdersHistoryInterfaceFactory $dataZeroDollarOrdersHistoryFactory
     * @param ZeroDollarOrdersHistoryCollectionFactory $zeroDollarOrdersHistoryCollectionFactory
     * @param ZeroDollarOrdersHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceZeroDollarOrdersHistory $resource,
        ZeroDollarOrdersHistoryFactory $zeroDollarOrdersHistoryFactory,
        ZeroDollarOrdersHistoryInterfaceFactory $dataZeroDollarOrdersHistoryFactory,
        ZeroDollarOrdersHistoryCollectionFactory $zeroDollarOrdersHistoryCollectionFactory,
        ZeroDollarOrdersHistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->zeroDollarOrdersHistoryFactory = $zeroDollarOrdersHistoryFactory;
        $this->zeroDollarOrdersHistoryCollectionFactory = $zeroDollarOrdersHistoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataZeroDollarOrdersHistoryFactory = $dataZeroDollarOrdersHistoryFactory;
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
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
    ) {
        /* if (empty($zeroDollarOrdersHistory->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $zeroDollarOrdersHistory->setStoreId($storeId);
        } */
        
        $zeroDollarOrdersHistoryData = $this->extensibleDataObjectConverter->toNestedArray(
            $zeroDollarOrdersHistory,
            [],
            \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface::class
        );
        
        $zeroDollarOrdersHistoryModel = $this->zeroDollarOrdersHistoryFactory->create()->setData($zeroDollarOrdersHistoryData);
        
        try {
            $this->resource->save($zeroDollarOrdersHistoryModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the zeroDollarOrdersHistory: %1',
                $exception->getMessage()
            ));
        }
        return $zeroDollarOrdersHistoryModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($zeroDollarOrdersHistoryId)
    {
        $zeroDollarOrdersHistory = $this->zeroDollarOrdersHistoryFactory->create();
        $this->resource->load($zeroDollarOrdersHistory, $zeroDollarOrdersHistoryId);
        if (!$zeroDollarOrdersHistory->getId()) {
            throw new NoSuchEntityException(__('zero_dollar_orders_history with id "%1" does not exist.', $zeroDollarOrdersHistoryId));
        }
        return $zeroDollarOrdersHistory->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->zeroDollarOrdersHistoryCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface::class
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
        \Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface $zeroDollarOrdersHistory
    ) {
        try {
            $zeroDollarOrdersHistoryModel = $this->zeroDollarOrdersHistoryFactory->create();
            $this->resource->load($zeroDollarOrdersHistoryModel, $zeroDollarOrdersHistory->getZeroDollarOrdersHistoryId());
            $this->resource->delete($zeroDollarOrdersHistoryModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the zero_dollar_orders_history: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($zeroDollarOrdersHistoryId)
    {
        return $this->delete($this->get($zeroDollarOrdersHistoryId));
    }
}

