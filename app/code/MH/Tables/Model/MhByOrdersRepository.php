<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Model;

use MH\Tables\Api\Data\MhByOrdersInterfaceFactory;
use MH\Tables\Api\Data\MhByOrdersSearchResultsInterfaceFactory;
use MH\Tables\Api\MhByOrdersRepositoryInterface;
use MH\Tables\Model\ResourceModel\MhByOrders as ResourceMhByOrders;
use MH\Tables\Model\ResourceModel\MhByOrders\CollectionFactory as MhByOrdersCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class MhByOrdersRepository implements MhByOrdersRepositoryInterface
{

    protected $mhByOrdersCollectionFactory;

    private $collectionProcessor;

    protected $dataObjectHelper;

    protected $resource;

    protected $searchResultsFactory;

    protected $dataMhByOrdersFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $mhByOrdersFactory;

    private $storeManager;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceMhByOrders $resource
     * @param MhByOrdersFactory $mhByOrdersFactory
     * @param MhByOrdersInterfaceFactory $dataMhByOrdersFactory
     * @param MhByOrdersCollectionFactory $mhByOrdersCollectionFactory
     * @param MhByOrdersSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceMhByOrders $resource,
        MhByOrdersFactory $mhByOrdersFactory,
        MhByOrdersInterfaceFactory $dataMhByOrdersFactory,
        MhByOrdersCollectionFactory $mhByOrdersCollectionFactory,
        MhByOrdersSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->mhByOrdersFactory = $mhByOrdersFactory;
        $this->mhByOrdersCollectionFactory = $mhByOrdersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMhByOrdersFactory = $dataMhByOrdersFactory;
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
        \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
    ) {
        /* if (empty($mhByOrders->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $mhByOrders->setStoreId($storeId);
        } */
        
        $mhByOrdersData = $this->extensibleDataObjectConverter->toNestedArray(
            $mhByOrders,
            [],
            \MH\Tables\Api\Data\MhByOrdersInterface::class
        );
        
        $mhByOrdersModel = $this->mhByOrdersFactory->create()->setData($mhByOrdersData);
        
        try {
            $this->resource->save($mhByOrdersModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the mhByOrders: %1',
                $exception->getMessage()
            ));
        }
        return $mhByOrdersModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($mhByOrdersId)
    {
        $mhByOrders = $this->mhByOrdersFactory->create();
        $this->resource->load($mhByOrders, $mhByOrdersId);
        if (!$mhByOrders->getId()) {
            throw new NoSuchEntityException(__('mh_by_orders with id "%1" does not exist.', $mhByOrdersId));
        }
        return $mhByOrders->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->mhByOrdersCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \MH\Tables\Api\Data\MhByOrdersInterface::class
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
        \MH\Tables\Api\Data\MhByOrdersInterface $mhByOrders
    ) {
        try {
            $mhByOrdersModel = $this->mhByOrdersFactory->create();
            $this->resource->load($mhByOrdersModel, $mhByOrders->getMhByOrdersId());
            $this->resource->delete($mhByOrdersModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the mh_by_orders: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($mhByOrdersId)
    {
        return $this->delete($this->get($mhByOrdersId));
    }
}

