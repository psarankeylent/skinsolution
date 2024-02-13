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

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\MessageSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class MessageRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @var MessageResourceModel
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
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @var MessageSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var MessageInterface[]
     */
    private $registry = [];

    /**
     * @param MessageResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param MessageInterfaceFactory $messageFactory
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        MessageResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        MessageInterfaceFactory $messageFactory,
        MessageCollectionFactory $messageCollectionFactory,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->messageFactory = $messageFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(MessageInterface $message)
    {
        try {
            $this->resource->save($message);
            $this->registry[$message->getId()] = $message;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function getById($messageId)
    {
        if (!isset($this->registry[$messageId])) {
            /** @var MessageInterface $message */
            $message = $this->messageFactory->create();
            $this->resource->load($message, $messageId);
            if (!$message->getId()) {
                throw NoSuchEntityException::singleField(MessageInterface::ID, $messageId);
            }
            $this->registry[$message->getId()] = $message;
        }

        return $this->registry[$messageId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var MessageCollection $collection */
        $collection = $this->messageCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, MessageInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var MessageSearchResultsInterface $searchResults */
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
    public function delete(MessageInterface $message)
    {
        try {
            $this->resource->delete($message);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$message->getId()])) {
            unset($this->registry[$message->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($messageId)
    {
        return $this->delete($this->getById($messageId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Message $model
     * @return MessageInterface
     */
    private function getDataObject($model)
    {
        /** @var MessageInterface $object */
        $object = $this->messageFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, MessageInterface::class),
            MessageInterface::class
        );

        return $object;
    }
}
