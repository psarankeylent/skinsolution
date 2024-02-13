<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface;
use Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterfaceFactory;
use Renewal\NotificationSchedule\Api\Data\NotificationScheduleSearchResultsInterfaceFactory;
use Renewal\NotificationSchedule\Api\NotificationScheduleRepositoryInterface;
use Renewal\NotificationSchedule\Model\ResourceModel\NotificationSchedule as ResourceNotificationSchedule;
use Renewal\NotificationSchedule\Model\ResourceModel\NotificationSchedule\CollectionFactory as NotificationScheduleCollectionFactory;

class NotificationScheduleRepository implements NotificationScheduleRepositoryInterface
{

    /**
     * @var ResourceNotificationSchedule
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var NotificationScheduleCollectionFactory
     */
    protected $notificationScheduleCollectionFactory;

    /**
     * @var NotificationSchedule
     */
    protected $searchResultsFactory;

    /**
     * @var NotificationScheduleInterfaceFactory
     */
    protected $notificationScheduleFactory;


    /**
     * @param ResourceNotificationSchedule $resource
     * @param NotificationScheduleInterfaceFactory $notificationScheduleFactory
     * @param NotificationScheduleCollectionFactory $notificationScheduleCollectionFactory
     * @param NotificationScheduleSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceNotificationSchedule $resource,
        NotificationScheduleInterfaceFactory $notificationScheduleFactory,
        NotificationScheduleCollectionFactory $notificationScheduleCollectionFactory,
        NotificationScheduleSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->notificationScheduleFactory = $notificationScheduleFactory;
        $this->notificationScheduleCollectionFactory = $notificationScheduleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        NotificationScheduleInterface $notificationSchedule
    ) {
        try {
            $this->resource->save($notificationSchedule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the notificationSchedule: %1',
                $exception->getMessage()
            ));
        }
        return $notificationSchedule;
    }

    /**
     * @inheritDoc
     */
    public function get($notificationScheduleId)
    {
        $notificationSchedule = $this->notificationScheduleFactory->create();
        $this->resource->load($notificationSchedule, $notificationScheduleId);
        if (!$notificationSchedule->getId()) {
            throw new NoSuchEntityException(__('NotificationSchedule with id "%1" does not exist.', $notificationScheduleId));
        }
        return $notificationSchedule;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->notificationScheduleCollectionFactory->create();
        
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
        NotificationScheduleInterface $notificationSchedule
    ) {
        try {
            $notificationScheduleModel = $this->notificationScheduleFactory->create();
            $this->resource->load($notificationScheduleModel, $notificationSchedule->getNotificationscheduleId());
            $this->resource->delete($notificationScheduleModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the NotificationSchedule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($notificationScheduleId)
    {
        return $this->delete($this->get($notificationScheduleId));
    }
}

