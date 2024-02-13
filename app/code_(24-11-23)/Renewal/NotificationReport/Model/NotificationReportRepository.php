<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Renewal\NotificationReport\Api\Data\NotificationReportInterface;
use Renewal\NotificationReport\Api\Data\NotificationReportInterfaceFactory;
use Renewal\NotificationReport\Api\Data\NotificationReportSearchResultsInterfaceFactory;
use Renewal\NotificationReport\Api\NotificationReportRepositoryInterface;
use Renewal\NotificationReport\Model\ResourceModel\NotificationReport as ResourceNotificationReport;
use Renewal\NotificationReport\Model\ResourceModel\NotificationReport\CollectionFactory as NotificationReportCollectionFactory;

class NotificationReportRepository implements NotificationReportRepositoryInterface
{

    /**
     * @var ResourceNotificationReport
     */
    protected $resource;

    /**
     * @var NotificationReportInterfaceFactory
     */
    protected $notificationReportFactory;

    /**
     * @var NotificationReportCollectionFactory
     */
    protected $notificationReportCollectionFactory;

    /**
     * @var NotificationReport
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceNotificationReport $resource
     * @param NotificationReportInterfaceFactory $notificationReportFactory
     * @param NotificationReportCollectionFactory $notificationReportCollectionFactory
     * @param NotificationReportSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceNotificationReport $resource,
        NotificationReportInterfaceFactory $notificationReportFactory,
        NotificationReportCollectionFactory $notificationReportCollectionFactory,
        NotificationReportSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->notificationReportFactory = $notificationReportFactory;
        $this->notificationReportCollectionFactory = $notificationReportCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        NotificationReportInterface $notificationReport
    ) {
        try {
            $this->resource->save($notificationReport);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the notificationReport: %1',
                $exception->getMessage()
            ));
        }
        return $notificationReport;
    }

    /**
     * @inheritDoc
     */
    public function get($notificationReportId)
    {
        $notificationReport = $this->notificationReportFactory->create();
        $this->resource->load($notificationReport, $notificationReportId);
        if (!$notificationReport->getId()) {
            throw new NoSuchEntityException(__('NotificationReport with id "%1" does not exist.', $notificationReportId));
        }
        return $notificationReport;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->notificationReportCollectionFactory->create();
        
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
        NotificationReportInterface $notificationReport
    ) {
        try {
            $notificationReportModel = $this->notificationReportFactory->create();
            $this->resource->load($notificationReportModel, $notificationReport->getNotificationreportId());
            $this->resource->delete($notificationReportModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the NotificationReport: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($notificationReportId)
    {
        return $this->delete($this->get($notificationReportId));
    }
}

