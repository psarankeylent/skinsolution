<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    //protected $_idFieldName = 'consultonly_id';
    protected $_idFieldName = 'id';

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
        $this->_init('ConsultOnly\ConsultOnlyCollection\Model\ConsultOnly', 'ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly');

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

        $collection = $this;
        //echo "<pre>"; print_r($collection->getData()); exit;


        return $collection;*/

    }
    public function getData1111()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');

         $collectionFactory = $objectManager->get('ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly\CollectionFactory');

         $collection = $collectionFactory->create();


        //$collection = $this;
       // echo "<pre>"; print_r($collection->getData()); exit;

        $date = new \DateTime();
        
        //$data = parent::getData();
        $expDays = $request->getParam('expiration_date');

        if(isset($expDays) && $expDays == 30)
        {
            $toDate     = $date->format('Y-m-d H:i:s');
            $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

            //$collection->addFieldToFilter('expiration_date', array('from'=>$fromDate, 'to'=>$toDate));
            $collection->addFieldToFilter('prescription_name', array('eq'=>'Latisse'));
        }
        elseif(isset($expDays) && $expDays == 60)
        {
            $toDate     = $date->format('Y-m-d H:i:s');
            $fromDate   = $date->modify("-60 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', array('from'=>$fromDate, 'to'=>$toDate));
        }
        elseif(isset($expDays) && $expDays == 90)
        {
            $toDate     = $date->format('Y-m-d H:i:s');
            $fromDate   = $date->modify("-90 days")->format('Y-m-d H:i:s');
            $collection->addFieldToFilter('expiration_date', array('from'=>$fromDate, 'to'=>$toDate));
        }


       return $collection->getData();
    

    }
}

