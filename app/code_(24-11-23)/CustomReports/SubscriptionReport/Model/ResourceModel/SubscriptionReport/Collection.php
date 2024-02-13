<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\SubscriptionReport\Model\ResourceModel\SubscriptionReport;

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
        $this->_init('CustomReports\SubscriptionReport\Model\SubscriptionReport', 'CustomReports\SubscriptionReport\Model\ResourceModel\SubscriptionReport');
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();

        $collection = $this;

        $collection->getSelect()->joinLeft(
            ['web_cust' => $this->getTable('ssmd_web_customers')],
            '`main_table`.customer_id = `web_cust`.customer_id', 
            ['email', 'name']
        );

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('name', 'web_cust.name');
        $collection->addFilterToMap('email', 'web_cust.email');

        //echo "<pre>"; print_r($collection->getData()); exit;
        return $collection;

    }

}

