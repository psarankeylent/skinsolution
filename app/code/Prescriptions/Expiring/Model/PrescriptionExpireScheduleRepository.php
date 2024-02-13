<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface;
use Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterfaceFactory;
use Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleSearchResultsInterfaceFactory;
use Prescriptions\Expiring\Api\PrescriptionExpireScheduleRepositoryInterface;
use Prescriptions\Expiring\Model\ResourceModel\PrescriptionExpireSchedule as ResourcePrescriptionExpireSchedule;
use Prescriptions\Expiring\Model\ResourceModel\PrescriptionExpireSchedule\CollectionFactory as PrescriptionExpireScheduleCollectionFactory;

class PrescriptionExpireScheduleRepository implements PrescriptionExpireScheduleRepositoryInterface
{

    /**
     * @var ResourcePrescriptionExpireSchedule
     */
    protected $resource;

    /**
     * @var PrescriptionExpireScheduleInterfaceFactory
     */
    protected $prescriptionExpireScheduleFactory;

    /**
     * @var PrescriptionExpireScheduleCollectionFactory
     */
    protected $prescriptionExpireScheduleCollectionFactory;

    /**
     * @var PrescriptionExpireSchedule
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourcePrescriptionExpireSchedule $resource
     * @param PrescriptionExpireScheduleInterfaceFactory $prescriptionExpireScheduleFactory
     * @param PrescriptionExpireScheduleCollectionFactory $prescriptionExpireScheduleCollectionFactory
     * @param PrescriptionExpireScheduleSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePrescriptionExpireSchedule $resource,
        PrescriptionExpireScheduleInterfaceFactory $prescriptionExpireScheduleFactory,
        PrescriptionExpireScheduleCollectionFactory $prescriptionExpireScheduleCollectionFactory,
        PrescriptionExpireScheduleSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->prescriptionExpireScheduleFactory = $prescriptionExpireScheduleFactory;
        $this->prescriptionExpireScheduleCollectionFactory = $prescriptionExpireScheduleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
    ) {
        try {
            $this->resource->save($prescriptionExpireSchedule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the prescriptionExpireSchedule: %1',
                $exception->getMessage()
            ));
        }
        return $prescriptionExpireSchedule;
    }

    /**
     * @inheritDoc
     */
    public function get($prescriptionExpireScheduleId)
    {
        $prescriptionExpireSchedule = $this->prescriptionExpireScheduleFactory->create();
        $this->resource->load($prescriptionExpireSchedule, $prescriptionExpireScheduleId);
        if (!$prescriptionExpireSchedule->getId()) {
            throw new NoSuchEntityException(__('prescription_expire_schedule with id "%1" does not exist.', $prescriptionExpireScheduleId));
        }
        return $prescriptionExpireSchedule;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->prescriptionExpireScheduleCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(
        PrescriptionExpireScheduleInterface $prescriptionExpireSchedule
    ) {
        try {
            $prescriptionExpireScheduleModel = $this->prescriptionExpireScheduleFactory->create();
            $this->resource->load($prescriptionExpireScheduleModel, $prescriptionExpireSchedule->getPrescriptionExpireScheduleId());
            $this->resource->delete($prescriptionExpireScheduleModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the prescription_expire_schedule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($prescriptionExpireScheduleId)
    {
        return $this->delete($this->get($prescriptionExpireScheduleId));
    }
}

