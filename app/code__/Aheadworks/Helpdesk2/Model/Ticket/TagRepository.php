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

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Api\Data\TagInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TagRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Collection as TagCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\CollectionFactory as TagCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class TagRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class TagRepository implements TagRepositoryInterface
{
    /**
     * @var TagResourceModel
     */
    private $resource;

    /**
     * @var TagInterfaceFactory
     */
    private $tagFactory;

    /**
     * @var TagCollectionFactory
     */
    private $tagCollectionFactory;

    /**
     * @var TagSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

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
     * @var TagInterface[]
     */
    private $registry = [];

    /**
     * @param TagResourceModel $resource
     * @param TagInterfaceFactory $tagFactory
     * @param TagCollectionFactory $tagCollectionFactory
     * @param TagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        TagResourceModel $resource,
        TagInterfaceFactory $tagFactory,
        TagCollectionFactory $tagCollectionFactory,
        TagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->tagFactory = $tagFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function get($tagId)
    {
        if (!isset($this->registry[$tagId])) {
            /** @var TagInterface $tag */
            $tag = $this->tagFactory->create();
            $this->resource->load($tag, $tagId);
            if (!$tag->getId()) {
                throw NoSuchEntityException::singleField(TagInterface::ID, $tagId);
            }
            $this->registry[$tag->getId()] = $tag;
        }

        return $this->registry[$tagId];
    }

    /**
     * {@inheritdoc}
     */
    public function save(TagInterface $tag)
    {
        try {
            $this->resource->save($tag);
            $this->registry[$tag->getId()] = $tag;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var TagCollection $collection */
        $collection = $this->tagCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TagInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TagSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $tags = [];
        /** @var Tag $tagModel */
        foreach ($collection as $tagModel) {
            $tags[] = $this->getTagDataObject($tagModel);
        }
        $searchResults->setItems($tags);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(TagInterface $tag)
    {
        try {
            $this->resource->delete($tag);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$tag->getId()])) {
            unset($this->registry[$tag->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($tagId)
    {
        return $this->delete($this->get($tagId));
    }

    /**
     * Retrieves tag data object using Tag Model
     *
     * @param Tag $tag
     * @return TagInterface
     */
    private function getTagDataObject(Tag $tag)
    {
        /** @var TagInterface $tagDataObject */
        $tagDataObject = $this->tagFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $tagDataObject,
            $tag->getData(),
            TagInterface::class
        );
        return $tagDataObject;
    }
}
