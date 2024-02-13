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
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Bup\Api\Data\UserProfileMetadataInterface;
use Aheadworks\Bup\Api\Data\UserProfileMetadataInterfaceFactory;
use Aheadworks\Bup\Api\Data\UserProfileMetadataSearchResultsInterface;
use Aheadworks\Bup\Api\Data\UserProfileMetadataSearchResultsInterfaceFactory as SearchResultFactory;
use Aheadworks\Bup\Api\UserProfileMetadataRepositoryInterface;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;
use Aheadworks\Bup\Model\ResourceModel\UserProfileFactory as UserProfileFactoryResource;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionExtended;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionExtendedFactory as UserProfileCollectionExtendedFactory;
use Aheadworks\Bup\Model\Source\UserProfile\Area as UserProfileArea;
use Magento\Setup\Module\ResourceFactory;

/**
 * Class UserProfileRepository
 *
 * @package Aheadworks\Bup\Model
 */
class UserProfileMetadataRepository implements UserProfileMetadataRepositoryInterface
{
    /**
     * @var UserProfileFactoryResource
     */
    private $resourceFactory;

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
     * @var UserProfileMetadataInterfaceFactory
     */
    private $userProfileMetadataFactory;

    /**
     * @var UserProfileCollectionExtendedFactory
     */
    private $userProfileCollectionExtendedFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param UserProfileFactoryResource $resourceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UserProfileMetadataInterfaceFactory $userProfileMetadataFactory
     * @param UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory
     * @param SearchResultFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        UserProfileFactoryResource $resourceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        UserProfileMetadataInterfaceFactory $userProfileMetadataFactory,
        UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory,
        SearchResultFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceFactory = $resourceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->userProfileMetadataFactory = $userProfileMetadataFactory;
        $this->userProfileCollectionExtendedFactory = $userProfileCollectionExtendedFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function get($userId, $area = UserProfileArea::STOREFRONT)
    {
        if (!isset($this->registry[$userId][$area])) {
            /** @var UserProfileResource $resource */
            $resource = $this->resourceFactory->create();
            $resource->setUserProfileArea($area);
            $userProfileData = $resource->loadExtended($userId);
            if (!$userProfileData) {
                throw NoSuchEntityException::singleField(UserProfileMetadataInterface::USER_ID, $userId);
            }

            /** @var UserProfileMetadataInterface $userProfile */
            $userProfileMetadata = $this->prepareDataObjectFromRowData($userProfileData);
            $this->registry[$userId][$area] = $userProfileMetadata;
        }

        return $this->registry[$userId][$area];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $area = UserProfileArea::STOREFRONT)
    {
        /** @var CollectionExtended $collection */
        $collection = $this->userProfileCollectionExtendedFactory->create();
        $collection->setUserProfileArea($area);
        $collection->reset();

        $this->extensionAttributesJoinProcessor->process($collection, UserProfileMetadataInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var UserProfileMetadataSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var UserProfileMetadata $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->prepareDataObjectFromModel($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object from model
     *
     * @param UserProfileMetadata|array $model
     * @return UserProfileMetadataInterface
     */
    private function prepareDataObjectFromModel($model)
    {
        /** @var UserProfileMetadataInterface $object */
        $object = $this->userProfileMetadataFactory->create();
        $dataArray = is_array($model)
            ? $model
            : $this->dataObjectProcessor->buildOutputDataArray($model, UserProfileMetadataInterface::class);
        $this->dataObjectHelper->populateWithArray($object, $dataArray, UserProfileMetadataInterface::class);

        return $object;
    }

    /**
     * Prepare data object from row data array
     *
     * @param array $dataArray
     * @return UserProfileMetadataInterface
     */
    private function prepareDataObjectFromRowData($dataArray)
    {
        $notFormattedDataObject = $this->prepareDataObjectFromModel($dataArray);
        $formattedData = $this->dataObjectProcessor->buildOutputDataArray(
            $notFormattedDataObject,
            UserProfileMetadataInterface::class
        );

        return $this->prepareDataObjectFromModel($formattedData);
    }
}
