<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\DeclinedSubscriptionsReportM1\Model\ResourceModel\DeclinedSubscriptionsReportM1;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id'; //primary id of the table

    /**
     * @param EntityFactoryInterface $entityFactory,
     * @param LoggerInterface        $logger,
     * @param FetchStrategyInterface $fetchStrategy,
     * @param ManagerInterface       $eventManager,
     * @param StoreManagerInterface  $storeManager,
     * @param AdapterInterface       $connection,
     * @param AbstractDb             $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_init('Renewal\DeclinedSubscriptionsReportM1\Model\DeclinedSubscriptionsReportM1', 'Renewal\DeclinedSubscriptionsReportM1\Model\ResourceModel\DeclinedSubscriptionsReportM1');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $collection = $this;

        $collection->getSelect()
            ->joinLeft('legacy_subscription', 'main_table.subscription_id = legacy_subscription.reference_id', array(''))
            ->joinLeft('customer_entity', 'legacy_subscription.customer_id = customer_entity.entity_id', array('email'));

        $collection->addFieldToFilter('subscription_id', ['neq' => null]);
        $collection->addFieldToFilter('generate_order', ['eq' => 'no']);

        // Map fields to avoid ambiguous column errors on filtering
         $collection->addFilterToMap('email', 'customer_entity.email');
        //$collection->addFilterToMap('reference_id', 'mg_aw_sarp2_profile.reference_id');


        return $collection;

    }
}

