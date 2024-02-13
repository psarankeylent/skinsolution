<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\OrderReport\Model\ResourceModel\OrderReport;

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
    //protected $_idFieldName = 'entity_id'; //primary id of the table

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
        $this->_init('Renewal\OrderReport\Model\OrderReport', 'Renewal\OrderReport\Model\ResourceModel\OrderReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $collection = $this;

        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');


        $collection->getSelect()
            ->joinLeft(array('so'=> 'sales_order'),
                'main_table.entity_id = so.entity_id', array("CONCAT(so.customer_firstname, ' ', so.customer_lastname) as customer_name"))
            ->joinLeft(array('soa'=> 'sales_order_address'),
                'soa.parent_id = main_table.entity_id', array('soa.region'))
            ->joinLeft( array('sig'=> 'sales_invoice_grid'),
                'sig.order_id = main_table.entity_id', array('sig.increment_id as invoice_number','sig.created_at as invoice_created'))
            ->joinLeft(array('soship'=> 'order_shipment'),
                'soship.increment_id = main_table.increment_id', array('soship.ship_date','soship.shipment_cost','soship.shipped_from'));

        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('increment_id', 'main_table.increment_id');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('region', 'soa.region');
        $collection->addFilterToMap('invoice_number', 'sig.increment_id');
        $collection->addFilterToMap('customer_email', 'main_table.customer_email');
        $collection->addFilterToMap('customer_firstname', 'main_table.customer_firstname');

        $collection->addFilterToMap('invoice_created', 'sig.created_at');
        $collection->addFilterToMap('ship_date', 'soship.ship_date');
        $collection->addFilterToMap('ship_cost', 'soship.ship_cost');
        $collection->addFilterToMap('warehouse', 'soship.warehouse');

        $collection->addFieldToFilter('soa.address_type','shipping');
        // echo "<pre>"; print_r($collection->getData()); exit;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

        $filters = $request->getParam('filters');
        // echo "<pre>"; print_r($filters); exit;
        if(isset($filters))
        {
            if(isset($filters['placeholder']))
            {
                unset($filters['placeholder']);
            }
            if(!empty($filters))
            {
                foreach ($filters as $key => $value) {
                    if($key == 'created_at' || $key == 'ship_date' || $key == 'invoice_created')
                    {
                        if(is_array($value))
                        {
                            if( (isset($value['from'])) && (isset($value['to'])) )
                            {
                                $fromDate = date("Y-m-d 00:00:00", strtotime($value['from']));
                                $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                            }
                            elseif( (!isset($value['from'])) && (isset($value['to'])) )
                            {
                                $fromDate = date("Y-m-d 00:00:00");
                                $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                            }
                            elseif( (isset($value['from'])) && (!isset($value['to'])) )
                            {
                                $fromDate = date("Y-m-d 00:00:00", strtotime($value['from']));
                                $toDate = date("Y-m-d 23:59:59");
                            }

                            $collection->addFieldToFilter($key, array('from'=>$fromDate, 'to'=>$toDate));
                        }
                    }
                    else{
                        $collection->addFieldToFilter($key, array('like' => $value));
                    }
                }
            }
            else
            {
                $collection->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
            }

        }
        else{
            $collection->addFieldToFilter('main_table.created_at', ['from'=>$fromDate,  'to'=>$toDate]);
        }
    }

}

