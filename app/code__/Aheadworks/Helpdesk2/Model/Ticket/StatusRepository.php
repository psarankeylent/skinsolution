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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\TicketStatusRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Status as StatusModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status\Collection as StatusCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Class StatusRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class StatusRepository implements TicketStatusRepositoryInterface
{
    /**
     * @var StatusResourceModel
     */
    private $resource;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var TicketStatusInterfaceFactory
     */
    private $ticketStatusFactory;

    /**
     * @var StatusCollectionFactory
     */
    private $ticketStatusCollectionFactory;

    /**
     * @var TicketStatusSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param StatusResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param TicketStatusInterfaceFactory $ticketStatusFactory
     * @param StatusCollectionFactory $ticketStatusCollectionFactory
     * @param TicketStatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        StatusResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        TicketStatusInterfaceFactory $ticketStatusFactory,
        StatusCollectionFactory $ticketStatusCollectionFactory,
        TicketStatusSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ticketStatusFactory = $ticketStatusFactory;
        $this->ticketStatusCollectionFactory = $ticketStatusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(TicketStatusInterface $status)
    {
        try {
            $this->resource->save($status);
            $this->registry[$status->getId()] = $status;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $status;
    }

    /**
     * @inheritdoc
     */
    public function get($statusId)
    {
        if (!isset($this->registry[$statusId])) {
            /** @var TicketStatusInterface $status */
            $status = $this->ticketStatusFactory->create();
            $this->resource->load($status, $statusId);
            if (!$status->getId()) {
                throw NoSuchEntityException::singleField(TicketStatusInterface::ID, $statusId);
            }
            $this->registry[$statusId] = $status;
        }

        return $this->registry[$statusId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var StatusCollection $collection */
        $collection = $this->ticketStatusCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TicketStatusInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TicketStatusSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var StatusModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param StatusModel $model
     * @return TicketStatusInterface
     */
    private function getDataObject($model)
    {
        /** @var TicketStatusInterface $object */
        $object = $this->ticketStatusFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, TicketStatusInterface::class),
            TicketStatusInterface::class
        );

        return $object;
    }
}
