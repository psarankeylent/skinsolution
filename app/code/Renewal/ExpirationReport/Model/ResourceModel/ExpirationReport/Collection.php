<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\ExpirationReport\Model\ResourceModel\ExpirationReport;

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
        $this->_init('Renewal\ExpirationReport\Model\ExpirationReport', 'Renewal\ExpirationReport\Model\ResourceModel\ExpirationReport');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $collection=$this;

        $query = $collection
            ->getSelect()
            ->joinLeft(
                ('customer_entity'),
                '`main_table`.customer_id = `customer_entity`.entity_id',
                [
                    'name'      => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)",
                    'email'         => "customer_entity.email"
                ]
            );

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('name', 'customer_entity.name');
        $collection->addFilterToMap('email', 'customer_entity.email');

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
                    if($key == 'expiration_date')
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
        }

        return $collection;
    }

    public function addFieldToFilter($field, $condition = null)
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
                $newCondition = ('`customer_entity`.`firstname` like "%'.$condition.'%" OR `customer_entity`.`lastname` = "%'.$condition.'%"');
                $this->getSelect()->where($newCondition);
            }


            //$this->getSelect()->where($conditionSql);  // Group by here beacause same data coming many times when filter of this field.
            // echo $this->getSelect(); exit;
            return $this;
        }

        if ($field === 'expiration_date') {

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
                        if($key == 'expiration_date')
                        {
                            if(is_array($value))
                            {
                                if( (isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));

                                    $select = $this->getSelect()->where('`expiration_date` >= "'.$fromDate.'" AND `expiration_date` <= "'.$toDate.'"');
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

