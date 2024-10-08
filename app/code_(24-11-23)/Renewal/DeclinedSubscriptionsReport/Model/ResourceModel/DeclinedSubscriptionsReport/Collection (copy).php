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
                            ->join('authnet_transaction_list', 'main_table.entity_id = authnet_transaction_list.subscription_id', array('subscription_id','generate_order'))
                            ->group('paradoxlabs_subscription_log.subscription_id')
                              //  'group by' is used because of subs_id have multiple values in a table 'paradoxlabs_subscription_log'.
                            ->order('paradoxlabs_subscription_log.subscription_id DESC');

        $collection->addFieldToFilter('paradoxlabs_subscription_log.status', ['eq' => 'payment_failed']);
        $collection->addFieldToFilter('authnet_transaction_list.subscription_id', ['neq' => null]);
        $collection->addFieldToFilter('authnet_transaction_list.generate_order', ['eq' => 'no']);

        /*$collection->getSelect()->group('paradoxlabs_subscription_log.subscription_id')
                            ->where('paradoxlabs_subscription_log.log_id IN((SELECT max(paradoxlabs_subscription_log.log_id) FROM `paradoxlabs_subscription_log` group by paradoxlabs_subscription_log.subscription_id))');*/
       
        foreach ($collection as $key => $value) {
            $logArr[] = $value['log_id'];
        }
        //$logArr = array(215,217,223,224);
        $collection->addFieldToFilter('log_id', array('in' => MAX($logArr)));

        //echo "<pre>"; print_r($collection->getData()); exit;
    
        // Map fields to avoid ambiguous column errors on filtering
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('customer_id', 'main_table.customer_id');
        $collection->addFilterToMap('updated_at', 'main_table.updated_at');
        $collection->addFilterToMap('frequency', 'main_table.frequency_count');
       
        
        if($filters = $request->getParam('filters'))
        {
            if(isset($filters['updated_at']))
            {
                $fromDate = date("Y-m-d H:i:s", strtotime($filters['updated_at']['from']));
                $toDate = date("Y-m-d 23:59:59", strtotime($filters['updated_at']['to']));
                $collection->addFieldToFilter('main_table.updated_at', ['from'=>$fromDate,  'to'=>$toDate]);

                $collection->getSelect()->group('paradoxlabs_subscription_log.subscription_id')
                            ->where('paradoxlabs_subscription_log.log_id IN((SELECT max(paradoxlabs_subscription_log.log_id) FROM `paradoxlabs_subscription_log` group by paradoxlabs_subscription_log.subscription_id))');
                
            }
            elseif(isset($filters['email']))
            {
                $collection->addFieldToFilter('customer_entity.email', ['eq' => $filters['email']]);

                $collection->getSelect()->group('paradoxlabs_subscription_log.subscription_id')
                            ->where('paradoxlabs_subscription_log.log_id IN((SELECT max(paradoxlabs_subscription_log.log_id) FROM `paradoxlabs_subscription_log` group by paradoxlabs_subscription_log.subscription_id))');
                
            }
            
        }
        
        return $collection;

    }

   /* public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            // Custom code for Filter
            if ($filter->getField() == 'frequency') {
                if ($filter->getValue() == 'aaa') {
                    $filterData = [
                        [
                            'attribute' => 'frequency',
                            'null' => true
                        ]
                    ];
                    $this->getCollection()->addAttributeToFilter($filterData);
                } else {
                    $filterData = [
                        [
                            'attribute' => 'frequency',
                            'eq' => $filter->getValue()
                        ]
                    ];
                    $this->getCollection()->addFieldToFilter($filterData);
                }
            }
            // End
            else {
                parent::addFilter($filter);
            }
        }
    }*/

}

