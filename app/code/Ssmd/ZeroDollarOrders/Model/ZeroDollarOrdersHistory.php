<?php
/**
 * Copyright © Zero Dollar Orders All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Model;

use Magento\Framework\Api\DataObjectHelper;
use Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterface;
use Ssmd\ZeroDollarOrders\Api\Data\ZeroDollarOrdersHistoryInterfaceFactory;

class ZeroDollarOrdersHistory extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'zero_dollar_orders_history';
    protected $dataObjectHelper;

    protected $zero_dollar_orders_historyDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ZeroDollarOrdersHistoryInterfaceFactory $zero_dollar_orders_historyDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory $resource
     * @param \Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ZeroDollarOrdersHistoryInterfaceFactory $zero_dollar_orders_historyDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory $resource,
        \Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory\Collection $resourceCollection,
        array $data = []
    ) {
        $this->zero_dollar_orders_historyDataFactory = $zero_dollar_orders_historyDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve zero_dollar_orders_history model with zero_dollar_orders_history data
     * @return ZeroDollarOrdersHistoryInterface
     */
    public function getDataModel()
    {
        $zero_dollar_orders_historyData = $this->getData();
        
        $zero_dollar_orders_historyDataObject = $this->zero_dollar_orders_historyDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $zero_dollar_orders_historyDataObject,
            $zero_dollar_orders_historyData,
            ZeroDollarOrdersHistoryInterface::class
        );
        
        return $zero_dollar_orders_historyDataObject;
    }
}

