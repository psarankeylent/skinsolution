<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\StoreCreditUsage\Model\ResourceModel\StoreCreditUsage;

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
        $this->_init('CustomReports\StoreCreditUsage\Model\StoreCreditUsage', 'CustomReports\StoreCreditUsage\Model\ResourceModel\StoreCreditUsage');

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
            ->join(
                ['quote_sc' => $this->getTable('quote_storecredit')],
                ('`main_table`.quote_id = `quote_sc`.quote_id'),
                ['quote_sc.applied_storecredit'])                       // Join because of only orders will display which has store credit.
            ->joinLeft(
                ['so' => $this->getTable('sales_order')],
                ('`main_table`.entity_id = `so`.entity_id'),
                ["CONCAT(main_table.customer_firstname, ' ', main_table.customer_lastname) as name"]);

        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('customer_email', 'so.customer_email');
        $collection->addFilterToMap('status', 'so.status');

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
                    if(isset($key['main_table']))
                    {
                        if($key['main_table'] == 'created_at')
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
            $collection->addFieldToFilter('main_table.created_at', ['from'=>$fromDate,  'to'=>$toDate]);
        }

    }

}

