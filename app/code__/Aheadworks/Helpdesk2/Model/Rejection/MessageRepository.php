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
namespace Aheadworks\Helpdesk2\Model\Rejection;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Api\RejectedMessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Message;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage as RejectedMessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage\Collection as RejectedMessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectedMessage\CollectionFactory as RejectedMessageCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RejectedMessageRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class MessageRepository implements RejectedMessageRepositoryInterface
{
    /**
     * @var RejectedMessageResourceModel
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
     * @var RejectedMessageInterfaceFactory
     */
    private $rejectedMessagesFactory;

    /**
     * @var RejectedMessageCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RejectedMessageSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var RejectedMessageInterface[]
     */
    private $registryById = [];

    /**
     * @param RejectedMessageResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RejectedMessageInterfaceFactory $rejectedMessageFactory
     * @param RejectedMessageCollectionFactory $collectionFactory
     * @param RejectedMessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        RejectedMessageResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        RejectedMessageInterfaceFactory $rejectedMessageFactory,
        RejectedMessageCollectionFactory $collectionFactory,
        RejectedMessageSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->rejectedMessagesFactory = $rejectedMessageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(RejectedMessageInterface $rejectedMessage)
    {
        try {
            $this->resource->save($rejectedMessage);
            $this->registryById[$rejectedMessage->getId()] = $rejectedMessage;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $rejectedMessage;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->registryById[$id])) {
            /** @var RejectedMessageInterface $rejectedMessage */
            $rejectedMessage = $this->rejectedMessagesFactory->create();
            $this->resource->load($rejectedMessage, $id);
            if (!$rejectedMessage->getId()) {
                throw NoSuchEntityException::singleField(RejectedMessageInterface::ID, $id);
            }
            $this->registryById[$rejectedMessage->getId()] = $rejectedMessage;
        }

        return $this->registryById[$id];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RejectedMessageCollection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RejectedMessageInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RejectedMessageSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Message $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(RejectedMessageInterface $rejectedMessage)
    {
        try {
            $this->resource->delete($rejectedMessage);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registryById[$rejectedMessage->getId()])) {
            unset($this->registryById[$rejectedMessage->getId()]);
        }

        return true;
    }

    /**
     * Retrieves data object using model
     *
     * @param Message $model
     * @return RejectedMessageInterface
     */
    private function getDataObject($model)
    {
        /** @var RejectedMessageInterface $object */
        $object = $this->rejectedMessagesFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, RejectedMessageInterface::class),
            RejectedMessageInterface::class
        );

        return $object;
    }
}
