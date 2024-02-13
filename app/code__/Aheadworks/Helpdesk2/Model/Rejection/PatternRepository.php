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

use Aheadworks\Helpdesk2\Model\Rejection\Pattern;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\CollectionFactory;

/**
 * Class PatternRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection
 */
class PatternRepository implements RejectingPatternRepositoryInterface
{
    /**
     * @var RejectingPatternResourceModel
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
     * @var RejectingPatternInterfaceFactory
     */
    private $patternFactory;

    /**
     * @var CollectionFactory
     */
    private $patternCollectionFactory;

    /**
     * @var RejectingPatternSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param RejectingPatternResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param RejectingPatternInterfaceFactory $patternFactory
     * @param CollectionFactory $patternCollectionFactory
     * @param RejectingPatternSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        RejectingPatternResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        RejectingPatternInterfaceFactory $patternFactory,
        CollectionFactory $patternCollectionFactory,
        RejectingPatternSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->patternFactory = $patternFactory;
        $this->patternCollectionFactory = $patternCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(RejectingPatternInterface $pattern)
    {
        try {
            $this->resource->save($pattern);
            $this->registry[$pattern->getId()] = $pattern;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $pattern;
    }

    /**
     * @inheritdoc
     */
    public function get($patternId)
    {
        if (!isset($this->registry[$patternId])) {
            /** @var RejectingPatternInterface $pattern */
            $pattern = $this->patternFactory->create();
            $this->resource->load($pattern, $patternId);
            if (!$pattern->getId()) {
                throw NoSuchEntityException::singleField(RejectingPatternInterface::ID, $patternId);
            }
            $this->registry[$patternId] = $pattern;
        }

        return $this->registry[$patternId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->patternCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RejectingPatternInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RejectingPatternSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Pattern $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(RejectingPatternInterface $pattern)
    {
        try {
            $this->resource->delete($pattern);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$pattern->getId()])) {
            unset($this->registry[$pattern->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($patternId)
    {
        return $this->delete($this->get($patternId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Pattern $model
     * @return RejectingPatternInterface
     */
    private function getDataObject($model)
    {
        /** @var RejectingPatternInterface $object */
        $object = $this->patternFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            RejectingPatternInterface::class
        );

        return $object;
    }
}
