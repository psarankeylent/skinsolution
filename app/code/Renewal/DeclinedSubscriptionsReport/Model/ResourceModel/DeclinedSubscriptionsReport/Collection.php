<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\DeclinedSubscriptionsReport\Model\ResourceModel\DeclinedSubscriptionsReport;

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
    protected $_idFieldName = 'entity_id'; //primary id of the table

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
        $this->_init('Renewal\DeclinedSubscriptionsReport\Model\DeclinedSubscriptionsReport', 'Renewal\DeclinedSubscriptionsReport\Model\ResourceModel\DeclinedSubscriptionsReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');

        parent::_initSelect();
        $collection = $this;

        $date = new \DateTime();
        $today = date('Y-m-d H:i:s');

        $collection->getSelect()
            ->join('paradoxlabs_subscription_log', 'main_table.entity_id = paradoxlabs_subscription_log.subscription_id', array('*'))
            ->join('customer_entity', 'main_table.customer_id = customer_entity.entity_id', array('email'))
            ->group('paradoxlabs_subscription_log.subscription_id');
        //  'group by' is used because of subs_id have multiple values in a table 'paradoxlabs_subscription_log'.
        $collection->addFieldToFilter('paradoxlabs_subscription_log.status', ['eq' => 'payment_failed']);
       // $collection->setOrder('paradoxlabs_subscription_log.subscription_id', 'DESC');

        // Map fields to avoid ambiguous column errors on filtering
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('customer_id', 'main_table.customer_id');
        $collection->addFilterToMap('updated_at', 'main_table.updated_at');
        $collection->addFilterToMap('frequency', 'main_table.frequency_count');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('description', 'main_table.description');

        return $collection;
    }

}


