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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterfaceFactory;
use Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface;
use Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterfaceFactory as SearchResultFactory;
use Aheadworks\Bup\Api\UserProfileRepositoryInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\Collection;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionFactory as UserProfileCollectionFactory;

/**
 * Class UserProfileRepository
 *
 * @package Aheadworks\Bup\Model
 */
class UserProfileRepository implements UserProfileRepositoryInterface
{
    /**
     * @var UserProfileResource
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
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var UserProfileCollectionFactory
     */
    private $userProfileCollectionFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param UserProfileResource $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param UserProfileCollectionFactory $userProfileCollectionFactory
     * @param SearchResultFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        UserProfileResource $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        UserProfileInterfaceFactory $userProfileFactory,
        UserProfileCollectionFactory $userProfileCollectionFactory,
        SearchResultFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->userProfileFactory = $userProfileFactory;
        $this->userProfileCollectionFactory = $userProfileCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(UserProfileInterface $userProfile)
    {
        try {
            $this->resource->save($userProfile);
            $userId = $userProfile->getUserId();
            $this->registry[$userId] = $userProfile;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $userProfile;
    }

    /**
     * @inheritdoc
     */
    public function get($userId)
    {
        if (!isset($this->registry[$userId])) {
            /** @var UserProfileInterface $userProfile */
            $userProfile = $this->userProfileFactory->create();
            $this->resource->load($userProfile, $userId);
            if (!$userProfile->getUserId()) {
                throw NoSuchEntityException::singleField(UserProfileInterface::USER_ID, $userId);
            }
            $this->registry[$userId] = $userProfile;
        }

        return $this->registry[$userId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->userProfileCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, UserProfileInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var UserProfileSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var UserProfile $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param UserProfile $model
     * @return UserProfileInterface
     */
    private function getDataObject($model)
    {
        /** @var UserProfileInterface $object */
        $object = $this->userProfileFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, UserProfileInterface::class),
            UserProfileInterface::class
        );

        return $object;
    }
}
