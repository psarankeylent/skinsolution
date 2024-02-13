<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\ImpersonationReport\Model\ResourceModel\Impersonation;

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
    // protected $_idFieldName = 'id'; //primary id of the table

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
        $this->_init('CustomReports\ImpersonationReport\Model\Impersonation', 'CustomReports\ImpersonationReport\Model\ResourceModel\Impersonation');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

        parent::_initSelect();

        $collection = $this;

        $collection->getSelect()
            ->joinLeft(
                ('admin_user'),
                '`main_table`.username = `admin_user`.username',
                [
                    'username'      => "CONCAT(admin_user.firstname, ' ', admin_user.lastname)"
                ]
            );

        $collection->getSelect()
            ->joinLeft(
                ('customer_entity'),
                '`main_table`.customer_email = `customer_entity`.email',
                [
                    'name'      => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)"
                ]
            );

        /*$collection->addFilterToMap('id', 'main_table.id');*/
        $collection->addFilterToMap('firstname', 'admin_user.firstname');
        $collection->addFilterToMap('lastname', 'admin_user.lastname');
        $collection->addFilterToMap('username', 'admin_user.username');
        $collection->addFilterToMap('firstname', 'customer_entity.firstname');
        $collection->addFilterToMap('lastname', 'customer_entity.lastname');

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

                                // $condition = $fromDate.'AND'.$toDate;
                                // $condition = (($fromDate.'AND'.$toDate));

                                //$this->addFieldToFilter($key, $condition);

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
                $collection->addFieldToFilter('created_date', ['lteq' => $toDate]);
                $collection->addFieldToFilter('created_date', ['gteq' => $fromDate]);
            }
        }
        else
        {
            $collection->addFieldToFilter('created_date', ['lteq' => $toDate]);
            $collection->addFieldToFilter('created_date', ['gteq' => $fromDate]);
        }

        //echo $collection->getSelect(); exit;
        //echo "<pre>"; print_r($collection->getData()); exit;
        return $collection;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'username') {
            $customerTable = $this->getConnection()->getTableName('admin_user');
            $this->getSelect()->joinLeft(
                [$customerTable],
                'main_table.username = admin_user.username',
                ['']
            );
            $conditionSql = $this->_getConditionSql(
                'CONCAT(`admin_user`.`firstname`, " ", `admin_user`.`lastname`)',  // means $field. here fname+lname
                $condition   // means value like '%yem kotina%'
            );
            $this->getSelect()->group('main_table.id')->where($conditionSql);  // Group by here beacause same data coming many times when filter of this field.
            //echo $this->getSelect(); exit;
            return $this;
        }
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
                        if($key == 'created_date')
                        {
                            if(is_array($value))
                            {
                                if( (isset($value['from'])) && (isset($value['to'])) )
                                {
                                    $fromDate = date("Y-m-d H:i:s", strtotime($value['from']));
                                    $toDate = date("Y-m-d 23:59:59", strtotime($value['to']));

                                    $select = $this->getSelect()->where('`created_date` >= "'.$fromDate.'" AND `created_date` <= "'.$toDate.'"');
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

