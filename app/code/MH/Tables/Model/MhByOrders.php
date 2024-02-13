<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Model;

use MH\Tables\Api\Data\MhByOrdersInterface;
use MH\Tables\Api\Data\MhByOrdersInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class MhByOrders extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'mh_by_orders';
    protected $mh_by_ordersDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param MhByOrdersInterfaceFactory $mh_by_ordersDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \MH\Tables\Model\ResourceModel\MhByOrders $resource
     * @param \MH\Tables\Model\ResourceModel\MhByOrders\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        MhByOrdersInterfaceFactory $mh_by_ordersDataFactory,
        DataObjectHelper $dataObjectHelper,
        \MH\Tables\Model\ResourceModel\MhByOrders $resource,
        \MH\Tables\Model\ResourceModel\MhByOrders\Collection $resourceCollection,
        array $data = []
    ) {
        $this->mh_by_ordersDataFactory = $mh_by_ordersDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve mh_by_orders model with mh_by_orders data
     * @return MhByOrdersInterface
     */
    public function getDataModel()
    {
        $mh_by_ordersData = $this->getData();
        
        $mh_by_ordersDataObject = $this->mh_by_ordersDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $mh_by_ordersDataObject,
            $mh_by_ordersData,
            MhByOrdersInterface::class
        );
        
        return $mh_by_ordersDataObject;
    }
}

