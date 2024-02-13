<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\StoreCreditReport\Model\ResourceModel\StoreCreditReport;

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
        $this->_init('CustomReports\StoreCreditReport\Model\StoreCreditReport', 'CustomReports\StoreCreditReport\Model\ResourceModel\StoreCreditReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $collection = $this;

        $select = $collection->getSelect();
        $select = $select->reset(\Zend_Db_Select::COLUMNS);
        $select->columns(array('id','customer_id', 'amount','comments','created_at'));

        $select->group('customer_id')
               ->where('id IN((SELECT max(id) FROM `storecredit` group by customer_id))');
        
        
        $collection->getSelect()
            ->joinLeft(
                ('customer_entity'),
                '`main_table`.customer_id = `customer_entity`.entity_id', 
                [
                    'firstname'      => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)",
                    'email'         => "customer_entity.email"
                ]
            );

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('firstname', 'customer_entity.firstname');
        $collection->addFilterToMap('lastname', 'customer_entity.lastname');
        $collection->addFilterToMap('email', 'customer_entity.email');

        //echo "<pre>"; print_r($collection->getData()); exit;
        return $collection;
    }

}

