<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\ReconciliationInvoiceReport\Model\ResourceModel\ReconciliationInvoiceReport;

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
        $this->_init('CustomReports\ReconciliationInvoiceReport\Model\ReconciliationInvoiceReport', 'CustomReports\ReconciliationInvoiceReport\Model\ResourceModel\ReconciliationInvoiceReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $collection = $this;

        $date = new \DateTime();
        $toDate     = date('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

        $collection->getSelect()
            ->joinLeft(
                ['s' => $this->getTable('order_shipment')],
                ('`main_table`.increment_id = `s`.increment_id'),
                ['s.ship_date','s.shipment_cost','s.shipped_from'])
            ->joinLeft(
                ['i' => $this->getTable('sales_invoice')],
                ('`main_table`.entity_id = `i`.order_id'),
                ['i.created_at as paid_date', 'i.tax_amount'])
            ->joinLeft(
                ['p' => $this->getTable('sales_order_payment')],
                ('`main_table`.entity_id = `p`.parent_id'),
                ['p.amount_paid'])
            ->joinLeft(
                ['a' => $this->getTable('sales_order_address')],
                ('`main_table`.shipping_address_id = `a`.entity_id'),
                ['a.region']);

        
        $collection->addFieldToFilter('main_table.status', ['like' => 'complete']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '91%']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '3%']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '1%']);

        $collection->addFilterToMap('increment_id', 'main_table.increment_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('paid_date', 'i.created_at');
        $collection->addFilterToMap('ship_date', 's.ship_date');
        $collection->addFilterToMap('shipment_cost', 's.shipment_cost');
        $collection->addFilterToMap('shipped_from', 's.shipped_from');
        $collection->addFilterToMap('created_at', 'i.created_at');
        $collection->addFilterToMap('tax_amount', 'i.tax_amount');
        $collection->addFilterToMap('amount_paid', 'p.amount_paid');
        $collection->addFilterToMap('region', 'a.region');

        //echo "<pre>"; print_r($collection->getData()); exit;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

        $filters = $request->getParam('filters');
        
        if(isset($filters))
        {
            if(isset($filters['placeholder']))
            {
                unset($filters['placeholder']);
            }
           
            if(!empty($filters))
            {
                foreach ($filters as $key => $value) {
                       
                    if( ($key == 'created_at')|| ($key == 'ship_date')||($key == 'paid_date') )
                    {
                        if(is_array($value))
                        {
                             //echo "<pre>"; print_r($filters); exit;
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
                            $collection->addFieldToFilter($key, ['from'=>$fromDate,  'to'=>$toDate]);
                        }
                    }
                }
            }
            else
            {
                $collection->addFieldToFilter('created_at', ['from'=>$fromDate,  'to'=>$toDate]);
            }

        }
        else{
           $collection->addFieldToFilter('main_table.created_at', ['from'=>$fromDate,  'to'=>$toDate]);
        }

    }

}

