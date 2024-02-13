<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\BatchListReport\Model\ResourceModel\BatchListReport;

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
        $this->_init('CustomReports\BatchListReport\Model\BatchListReport', 'CustomReports\BatchListReport\Model\ResourceModel\BatchListReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

        parent::_initSelect();

        $date = new \DateTime();
        $toDate     = date('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

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
                // echo "<pre>"; print_r($filters); exit;
                foreach ($filters as $key => $value) {
                    if($key == 'batch_process_date') 
                    {
                        if(is_array($value))
                        {

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

                            //echo "<pre>"; print_r($fromDate); print_r($toDate); exit;
                            $this->addFieldToFilter('batch_process_date', ['from'=>$fromDate,  'to'=>$toDate]);
                        }
                    }
                        
                }
                
            }
            else
            {
               $this->addFieldToFilter('batch_process_date', ['from'=>$fromDate,  'to'=>$toDate]);
            }
        
        }
        else{
           $this->addFieldToFilter('batch_process_date', ['from'=>$fromDate,  'to'=>$toDate]);
        }

        return $this;
    }

}

