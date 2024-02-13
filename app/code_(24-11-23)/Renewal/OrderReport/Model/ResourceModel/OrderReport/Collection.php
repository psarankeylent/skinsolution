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
        $this->_init('Renewal\OrderReport\Model\OrderReport', 'Renewal\OrderReport\Model\ResourceModel\OrderReport');

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


        $collection->getSelect()->joinLeft(array('soa'=> 'sales_order_address'),
            'soa.parent_id = main_table.entity_id', array('soa.region'))
            ->joinLeft( array('sig'=> 'sales_invoice_grid'),
                'sig.order_id = main_table.entity_id', array('sig.increment_id as invoice_number'));


        //echo $query; exit;
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('invoice_number', 'sig.increment_id');

        //$collection->addFieldToFilter('main_table.created_at',['from'=> $fromDate,  'to'=>$toDate]);
        $collection->addFieldToFilter('soa.address_type','shipping');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

        $filters = $request->getParam('filters');
        //echo "<pre>"; print_r($filters); exit;
        if(isset($filters))
        {
            if(isset($filters['placeholder']))
            {
                unset($filters['placeholder']);
            }
            if(!empty($filters))
            {
                 //echo "<pre>"; print_r($filters); exit;
                foreach ($filters as $key => $value) {
                    if(isset($key['main_table']))
                    {
                        if($key['main_table'] == 'created_at') 
                        {
                            if(is_array($value))
                            {
                                /*$fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                $toDate = date("Y-m-d H:i:s", strtotime($value['to']));*/

                                if( (isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d H:i:s", strtotime($value['to']));
                                }
                                elseif( (!isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s");
                                    $toDate = date("Y-m-d H:i:s", strtotime($value['to']));
                                }
                                elseif( (isset($value['from'])) && (!isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d H:i:s");
                                }
                                
                                $collection->addFieldToFilter($key, ['from'=>$fromDate,  'to'=>$toDate]);
                            }
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
           // echo "adfasdfasdfsad"; exit;

           $collection->addFieldToFilter('main_table.created_at', ['from'=>$fromDate,  'to'=>$toDate]);
           
            
           // echo "<pre>"; print_r($collection->getData()); exit;
        }
    }

}

