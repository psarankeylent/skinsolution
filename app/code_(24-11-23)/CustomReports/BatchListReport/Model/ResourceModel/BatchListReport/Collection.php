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
        parent::_initSelect();

        $collection=$this;

        $date = new \DateTime();
        $toDate     = date('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

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
                // echo "<pre>"; print_r($filters); exit;
                foreach ($filters as $key => $value) {
                    if($key == 'created_date')
                    {
                        if(is_array($value))
                        {
                            if( (isset($value['from'])) && (isset($value['to'])) )
                            {
                                $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                            }
                            elseif( (!isset($value['from'])) && (isset($value['to'])) )
                            {
                                $fromDate = date("1970-01-01 00:00:00");
                                $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                            }
                            elseif( (isset($value['from'])) && (!isset($value['to'])) )
                            {
                                $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                $toDate = date("Y-m-d 23:59:59");
                            }

                            $collection->addFieldToFilter($key, array('from'=>$fromDate, 'to'=>$toDate));
                            //echo $collection->getSelect(); exit;
                        }
                    }
                }
            }
            else
            {
                $collection->addFieldToFilter('batch_process_date', ['lteq' => $toDate]);
                $collection->addFieldToFilter('batch_process_date', ['gteq' => $fromDate]);
            }
        }
        else
        {
            $collection->addFieldToFilter('batch_process_date', ['lteq' => $toDate]);
            $collection->addFieldToFilter('batch_process_date', ['gteq' => $fromDate]);
        }
       

        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'created_date') {

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
                    // echo "<pre>"; print_r($filters); exit;
                    foreach ($filters as $key => $value) {
                        if($key == 'batch_process_date')
                        {
                            if(is_array($value))
                            {
                                if( (isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));

                                    $select = $this->getSelect()->where('`batch_process_date` >= "'.$fromDate.'" AND `batch_process_date` <= "'.$toDate.'"'); 
                                    //echo $select; exit;

                                    return $this; 
                                }
                                elseif( (!isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("1970-01-01 00:00:00");
                                    $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                                }
                                elseif( (isset($value['from'])) && (!isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d 23:59:59");
                                }
                            }
                        }
                    }
                }
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

}

