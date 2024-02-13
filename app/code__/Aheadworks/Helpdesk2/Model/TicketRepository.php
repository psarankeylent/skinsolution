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
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;

/**
 * Class TicketRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class TicketRepository implements TicketRepositoryInterface
{
    /**
     * @var TicketResourceModel
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
     * @var TicketInterfaceFactory
     */
    private $ticketFactory;

    /**
     * @var TicketCollectionFactory
     */
    private $ticketCollectionFactory;

    /**
     * @var TicketSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TicketInterface[]
     */
    private $registryById = [];

    /**
     * @var TicketInterface[]
     */
    private $registryByUId = [];

    /**
     * @var TicketInterface[]
     */
    private $registryByLink = [];

    /**
     * @param TicketResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param TicketInterfaceFactory $ticketFactory
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param TicketSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        TicketResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        TicketInterfaceFactory $ticketFactory,
        TicketCollectionFactory $ticketCollectionFactory,
        TicketSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ticketFactory = $ticketFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(TicketInterface $ticket)
    {
        try {
            $this->resource->save($ticket);
            $this->registryById[$ticket->getEntityId()] = $ticket;
            $this->registryByUId[$ticket->getUid()] = $ticket;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $ticket;
    }

    /**
     * @inheritdoc
     */
    public function getById($ticketId, $reload = false)
    {
        if (!isset($this->registryById[$ticketId]) || $reload) {
            /** @var TicketInterface $ticket */
            $ticket = $this->ticketFactory->create();
            $this->resource->load($ticket, $ticketId);
            if (!$ticket->getEntityId()) {
                throw NoSuchEntityException::singleField(TicketInterface::ENTITY_ID, $ticketId);
            }
            $this->registryById[$ticket->getEntityId()] = $ticket;
            $this->registryByUId[$ticket->getUid()] = $ticket;
            $this->registryByLink[$ticket->getExternalLink()] = $ticket;
        }

        return $this->registryById[$ticketId];
    }

    /**
     * @inheritdoc
     */
    public function getByUid($uId)
    {
        if (!isset($this->registryByUId[$uId])) {
            $ticketId = $this->resource->getTicketIdByUid($uId);
            if (!$ticketId) {
                throw NoSuchEntityException::singleField(TicketInterface::UID, $uId);
            }

            $ticket = $this->getById($ticketId);
            $this->registryByUId[$ticket->getUid()] = $ticket;
        }

        return $this->registryByUId[$uId];
    }

    /**
     * @inheritdoc
     */
    public function getByExternalLink($link)
    {
        if (!isset($this->registryByLink[$link])) {
            $ticketId = $this->resource->getTicketIdByExternalLink($link);
            if (!$ticketId) {
                throw NoSuchEntityException::singleField(TicketInterface::EXTERNAL_LINK, $link);
            }

            $ticket = $this->getById($ticketId);
            $this->registryByLink[$ticket->getExternalLink()] = $ticket;
        }

        return $this->registryByLink[$link];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var TicketCollection $collection */
        $collection = $this->ticketCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TicketInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TicketSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Ticket $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(TicketInterface $ticket)
    {
        try {
            $this->resource->delete($ticket);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registryById[$ticket->getEntityId()])) {
            unset($this->registryById[$ticket->getEntityId()]);
        }
        if (isset($this->registryByUId[$ticket->getUid()])) {
            unset($this->registryByUId[$ticket->getUid()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($ticketId)
    {
        return $this->delete($this->getById($ticketId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Ticket $model
     * @return TicketInterface
     */
    private function getDataObject($model)
    {
        /** @var TicketInterface $object */
        $object = $this->ticketFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, TicketInterface::class),
            TicketInterface::class
        );

        return $object;
    }
}
