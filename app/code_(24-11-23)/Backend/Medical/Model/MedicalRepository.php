<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\Medical\Model;

use Backend\Medical\Api\Data\MedicalInterfaceFactory;
use Backend\Medical\Api\Data\MedicalSearchResultsInterfaceFactory;
use Backend\Medical\Api\MedicalRepositoryInterface;
use Backend\Medical\Model\ResourceModel\Medical as ResourceMedical;
use Backend\Medical\Model\ResourceModel\Medical\CollectionFactory as MedicalCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class MedicalRepository implements MedicalRepositoryInterface
{

    protected $resource;

    protected $medicalFactory;

    protected $medicalCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataMedicalFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceMedical $resource
     * @param MedicalFactory $medicalFactory
     * @param MedicalInterfaceFactory $dataMedicalFactory
     * @param MedicalCollectionFactory $medicalCollectionFactory
     * @param MedicalSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceMedical $resource,
        MedicalFactory $medicalFactory,
        MedicalInterfaceFactory $dataMedicalFactory,
        MedicalCollectionFactory $medicalCollectionFactory,
        MedicalSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->medicalFactory = $medicalFactory;
        $this->medicalCollectionFactory = $medicalCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMedicalFactory = $dataMedicalFactory;
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
        \Backend\Medical\Api\Data\MedicalInterface $medical
    ) {
        /* if (empty($medical->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $medical->setStoreId($storeId);
        } */
        
        $medicalData = $this->extensibleDataObjectConverter->toNestedArray(
            $medical,
            [],
            \Backend\Medical\Api\Data\MedicalInterface::class
        );
        
        $medicalModel = $this->medicalFactory->create()->setData($medicalData);
        
        try {
            $this->resource->save($medicalModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the medical: %1',
                $exception->getMessage()
            ));
        }
        return $medicalModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($medicalId)
    {
        $medical = $this->medicalFactory->create();
        $this->resource->load($medical, $medicalId);
        if (!$medical->getId()) {
            throw new NoSuchEntityException(__('Medical with id "%1" does not exist.', $medicalId));
        }
        return $medical->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->medicalCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Backend\Medical\Api\Data\MedicalInterface::class
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
        \Backend\Medical\Api\Data\MedicalInterface $medical
    ) {
        try {
            $medicalModel = $this->medicalFactory->create();
            $this->resource->load($medicalModel, $medical->getMedicalId());
            $this->resource->delete($medicalModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Medical: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($medicalId)
    {
        return $this->delete($this->get($medicalId));
    }
}

