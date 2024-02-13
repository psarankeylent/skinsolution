<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\CcReports\Model\ResourceModel\CcReports;

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
    //protected $_idFieldName = 'id'; //primary id of the table

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
        $this->_init('Renewal\CcReports\Model\CcReports', 'Renewal\CcReports\Model\ResourceModel\CcReports');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $collection = $this;

        $now                = new \DateTime();
        $fromDate           = $now->format('Y-m-d 00:00:00');
        $toDate             = $now->modify("+30days")->format('Y-m-d 00:00:00');
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();

        $collection
            ->getSelect()
            ->join(['sbs' => 'paradoxlabs_subscription'], 'main_table.customer_id = sbs.customer_id', ['quote_id','customer_id','status','next_run'])
            ->join(['item' => 'quote_item'], 'item.quote_id = sbs.quote_id', ['name','sku'])
            ->joinLeft(
                ('customer_entity'),
                'main_table.customer_id = customer_entity.entity_id',
                [
                    'firstname'          => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)",
                    'email'              => "customer_entity.email"
                ])
            ->where('sbs.status = ?', 'active')
            ->group('main_table.id');

        //echo "<pre>"; print_r($collection->getData()); exit;


        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('firstname', 'customer_entity.firstname');
        $collection->addFilterToMap('email', 'customer_entity.email');

        //echo "<pre>"; print_r($collection->getData()); exit;

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

                    if($key == 'expires')
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
                        }
                    }
                    elseif($key == 'email')
                    {
                        //$collection->addFieldToFilter('expires', array('from'=>$fromDate, 'to'=>$toDate));
                        $collection->addFieldToFilter('email', ['like' => $value]);
                    }
                    elseif($key == 'firstname')
                    {
                       // $collection->addFieldToFilter('expires', array('from'=>$fromDate, 'to'=>$toDate));
                        $collection->addFieldToFilter('firstname', ['like' => '%'.$value.'%']);
                    }
                }
            }
            else
            {
                $collection->addFieldToFilter('expires', array('from'=>$fromDate, 'to'=>$toDate));
            }
        }
        else{
            $collection->addFieldToFilter('expires', ['from'=>$fromDate,  'to'=>$toDate]);
        }
        return $collection;
    }
    /*public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'name') {

            if(is_array($condition))
            {
                $condition = $condition['like'];
            }
            if (strpos(trim($condition), ' ') !== false) {      // If fname & lname searches
                $conditionArr = explode(' ', $condition);
                $this->getSelect()->where('`customer_entity`.`firstname` like "%'.$conditionArr[0].'%" AND `customer_entity`.`lastname` like "%'.$conditionArr[1].'%"');
            }
            else  // If only firstname/lastname searches
            {
                $this->getSelect()->where('`customer_entity`.`firstname` like "%'.$condition.'%" OR `customer_entity`.`lastname` like "%'.$condition.'%"');
            }
            return $this;
        }

        if ($field === 'expires') {

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
                        if($key == 'expires')
                        {
                            if(is_array($value))
                            {
                                if( (isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));
                                    $select = $this->getSelect()->where('`expires` >= "'.$fromDate.'" AND `expires` <= "'.$toDate.'"');
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
    }*/
}

