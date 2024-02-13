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
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\Store;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\Collection as QuickResponseCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\CollectionFactory as QuickResponseCollectionFactory;

/**
 * Class QuickResponseRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class QuickResponseRepository implements QuickResponseRepositoryInterface
{
    /**
     * @var QuickResponseResourceModel
     */
    private $resource;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var QuickResponseInterfaceFactory
     */
    private $quickResponseFactory;

    /**
     * @var QuickResponseCollectionFactory
     */
    private $quickResponseCollectionFactory;

    /**
     * @var QuickResponseSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param QuickResponseResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param QuickResponseInterfaceFactory $quickResponseFactory
     * @param QuickResponseCollectionFactory $quickResponseCollectionFactory
     * @param QuickResponseSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        QuickResponseResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        QuickResponseInterfaceFactory $quickResponseFactory,
        QuickResponseCollectionFactory $quickResponseCollectionFactory,
        QuickResponseSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->quickResponseFactory = $quickResponseFactory;
        $this->quickResponseCollectionFactory = $quickResponseCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(QuickResponseInterface $quickResponse)
    {
        try {
            $this->resource->save($quickResponse);
            $this->registry[$quickResponse->getId()] = $quickResponse;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $quickResponse;
    }

    /**
     * @inheritdoc
     */
    public function get($quickResponseId, $storeId = null)
    {
        if (!isset($this->registry[$quickResponseId])) {
            /** @var QuickResponseInterface $quickResponse */
            $quickResponse = $this->quickResponseFactory->create();
            $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
            $this->resource->setArgumentsForEntity('store_id', $storeId);
            $this->resource->load($quickResponse, $quickResponseId);
            if (!$quickResponse->getId()) {
                throw NoSuchEntityException::singleField(QuickResponseInterface::ID, $quickResponseId);
            }
            $this->registry[$quickResponseId] = $quickResponse;
        }

        return $this->registry[$quickResponseId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var QuickResponseCollection $collection */
        $collection = $this->quickResponseCollectionFactory->create();
        $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
        $collection->setStoreId($storeId);

        $this->extensionAttributesJoinProcessor->process($collection, QuickResponseInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var QuickResponseSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var QuickResponse $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(QuickResponseInterface $quickResponse)
    {
        try {
            $this->resource->delete($quickResponse);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$quickResponse->getId()])) {
            unset($this->registry[$quickResponse->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($quickResponseId)
    {
        return $this->delete($this->get($quickResponseId));
    }

    /**
     * Retrieves data object using model
     *
     * @param QuickResponse $model
     * @return QuickResponseInterface
     */
    private function getDataObject($model)
    {
        /** @var QuickResponseInterface $object */
        $object = $this->quickResponseFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            QuickResponseInterface::class
        );

        return $object;
    }
}
