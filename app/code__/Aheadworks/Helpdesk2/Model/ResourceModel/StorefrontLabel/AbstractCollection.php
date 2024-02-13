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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel;

use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection as BaseAbstractCollection;
use Aheadworks\Helpdesk2\Model\StorefrontLabel\Resolver;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelEntityInterface;

/**
 * Class AbstractCollection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel
 */
abstract class AbstractCollection extends BaseAbstractCollection
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var Resolver
     */
    protected $storefrontLabelResolver;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Resolver $storefrontLabelResolver
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Resolver $storefrontLabelResolver,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storefrontLabelResolver = $storefrontLabelResolver;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Set store ID for entity labels retrieving
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get store ID for entity labels retrieving
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        $this->attachLabels();
        $this->addCurrentLabels();
        return parent::_afterLoad();
    }

    /**
     * Attach labels on storefront per store view
     *
     * @return $this
     */
    protected function attachLabels()
    {
        return $this->attachRelationTable(
            Repository::MAIN_TABLE_NAME,
            $this->getIdFieldName(),
            'entity_id',
            [
                StorefrontLabelInterface::CONTENT,
                StorefrontLabelInterface::STORE_ID
            ],
            StorefrontLabelEntityInterface::STOREFRONT_LABELS,
            [
                [
                    'field' => 'entity_type',
                    'condition' => '=',
                    'value' => $this->getStorefrontLabelEntityType()
                ]
            ],
            [
                'field' => StorefrontLabelInterface::STORE_ID,
                'direction' => SortOrder::SORT_ASC
            ],
            true
        );
    }

    /**
     * Retrieve type of entity with storefront labels
     *
     * @return string
     */
    abstract protected function getStorefrontLabelEntityType();

    /**
     * Add labels on storefront for specific store view
     *
     * @return $this
     */
    protected function addCurrentLabels()
    {
        $currentStoreId = $this->getStoreId();
        if (isset($currentStoreId)) {
            foreach ($this as $item) {
                $labelsData = $item->getData(StorefrontLabelEntityInterface::STOREFRONT_LABELS);
                if (is_array($labelsData)) {
                    $currentLabelsRecord = $this->storefrontLabelResolver
                        ->getLabelsForStoreAsArray($labelsData, $currentStoreId);
                    $item->setData(StorefrontLabelEntityInterface::CURRENT_STOREFRONT_LABEL, $currentLabelsRecord);
                }
            }
        }

        return $this;
    }
}
