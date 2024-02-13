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
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection as GatewayCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory as GatewayCollectionFactory;

/**
 * Class GatewayRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class GatewayRepository implements GatewayRepositoryInterface
{
    /**
     * @var GatewayResourceModel
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
     * @var GatewayDataInterfaceFactory
     */
    private $gatewayFactory;

    /**
     * @var GatewayCollectionFactory
     */
    private $gatewayCollectionFactory;

    /**
     * @var GatewayDataSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param GatewayResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param GatewayDataInterfaceFactory $gatewayFactory
     * @param GatewayCollectionFactory $gatewayCollectionFactory
     * @param GatewayDataSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        GatewayResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        GatewayDataInterfaceFactory $gatewayFactory,
        GatewayCollectionFactory $gatewayCollectionFactory,
        GatewayDataSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->gatewayFactory = $gatewayFactory;
        $this->gatewayCollectionFactory = $gatewayCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(GatewayDataInterface $gateway)
    {
        try {
            $this->resource->save($gateway);
            $this->registry[$gateway->getId()] = $gateway;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $gateway;
    }

    /**
     * @inheritdoc
     */
    public function get($gatewayId)
    {
        if (!isset($this->registry[$gatewayId])) {
            /** @var GatewayDataInterface $gateway */
            $gateway = $this->gatewayFactory->create();
            $this->resource->load($gateway, $gatewayId);
            if (!$gateway->getId()) {
                throw NoSuchEntityException::singleField(GatewayDataInterface::ID, $gatewayId);
            }
            $this->registry[$gatewayId] = $gateway;
        }

        return $this->registry[$gatewayId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var GatewayCollection $collection */
        $collection = $this->gatewayCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, GatewayDataInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var GatewayDataSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Gateway $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(GatewayDataInterface $gateway)
    {
        try {
            $this->resource->delete($gateway);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$gateway->getId()])) {
            unset($this->registry[$gateway->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($gatewayId)
    {
        return $this->delete($this->get($gatewayId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Department $model
     * @return GatewayDataInterface
     */
    private function getDataObject($model)
    {
        /** @var GatewayDataInterface $object */
        $object = $this->gatewayFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, GatewayDataInterface::class),
            GatewayDataInterface::class
        );

        return $object;
    }
}
