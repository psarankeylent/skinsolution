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
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Collection as AutomationCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\CollectionFactory as AutomationCollectionFactory;

/**
 * Class AutomationRepository
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class AutomationRepository implements AutomationRepositoryInterface
{
    /**
     * @var AutomationResourceModel
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
     * @var AutomationInterfaceFactory
     */
    private $automationFactory;

    /**
     * @var AutomationCollectionFactory
     */
    private $automationCollectionFactory;

    /**
     * @var AutomationSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param AutomationResourceModel $resource
     * @param DataObjectHelper $dataObjectHelper
     * @param AutomationInterfaceFactory $automationFactory
     * @param AutomationCollectionFactory $automationCollectionFactory
     * @param AutomationSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        AutomationResourceModel $resource,
        DataObjectHelper $dataObjectHelper,
        AutomationInterfaceFactory $automationFactory,
        AutomationCollectionFactory $automationCollectionFactory,
        AutomationSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->automationFactory = $automationFactory;
        $this->automationCollectionFactory = $automationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(AutomationInterface $automation)
    {
        try {
            $this->resource->save($automation);
            $this->registry[$automation->getId()] = $automation;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $automation;
    }

    /**
     * @inheritdoc
     */
    public function get($automationId)
    {
        if (!isset($this->registry[$automationId])) {
            /** @var AutomationInterface $automation */
            $automation = $this->automationFactory->create();
            $this->resource->load($automation, $automationId);
            if (!$automation->getId()) {
                throw NoSuchEntityException::singleField(AutomationInterface::ID, $automationId);
            }
            $this->registry[$automationId] = $automation;
        }

        return $this->registry[$automationId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var AutomationCollection $collection */
        $collection = $this->automationCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, AutomationInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var AutomationSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Automation $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(AutomationInterface $automation)
    {
        try {
            $this->resource->delete($automation);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$automation->getId()])) {
            unset($this->registry[$automation->getId()]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($automationId)
    {
        return $this->delete($this->get($automationId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Automation $model
     * @return AutomationInterface
     */
    private function getDataObject($model)
    {
        /** @var AutomationInterface $object */
        $object = $this->automationFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            AutomationInterface::class
        );

        return $object;
    }
}
