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
use Aheadworks\Helpdesk2\Api\TicketPriorityRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketPrioritySearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPrioritySearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Priority as PriorityModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority\Collection as PriorityCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority\CollectionFactory as PriorityCollectionFactory;

/**
 * Class PriorityRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class PriorityRepository implements TicketPriorityRepositoryInterface
{
    /**
     * @var PriorityResourceModel
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
     * @var TicketPriorityInterfaceFactory
     */
    private $ticketPriorityFactory;

    /**
     * @var PriorityCollectionFactory
     */
    private $ticketPriorityCollectionFactory;

    /**
     * @var TicketPrioritySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param PriorityResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param TicketPriorityInterfaceFactory $ticketPriorityFactory
     * @param PriorityCollectionFactory $ticketPriorityCollectionFactory
     * @param TicketPrioritySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        PriorityResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        TicketPriorityInterfaceFactory $ticketPriorityFactory,
        PriorityCollectionFactory $ticketPriorityCollectionFactory,
        TicketPrioritySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ticketPriorityFactory = $ticketPriorityFactory;
        $this->ticketPriorityCollectionFactory = $ticketPriorityCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(TicketPriorityInterface $priority)
    {
        try {
            $this->resource->save($priority);
            $this->registry[$priority->getId()] = $priority;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $priority;
    }

    /**
     * @inheritdoc
     */
    public function get($priorityId)
    {
        if (!isset($this->registry[$priorityId])) {
            /** @var TicketPriorityInterface $priority */
            $priority = $this->ticketPriorityFactory->create();
            $this->resource->load($priority, $priorityId);
            if (!$priority->getId()) {
                throw NoSuchEntityException::singleField(TicketPriorityInterface::ID, $priorityId);
            }
            $this->registry[$priorityId] = $priority;
        }

        return $this->registry[$priorityId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var PriorityCollection $collection */
        $collection = $this->ticketPriorityCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TicketPriorityInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TicketPrioritySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var PriorityModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param PriorityModel $model
     * @return TicketPriorityInterface
     */
    private function getDataObject($model)
    {
        /** @var TicketPriorityInterface $object */
        $object = $this->ticketPriorityFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, TicketPriorityInterface::class),
            TicketPriorityInterface::class
        );

        return $object;
    }
}
