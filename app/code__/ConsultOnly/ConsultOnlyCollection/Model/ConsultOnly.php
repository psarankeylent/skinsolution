<?php

declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Model;

use ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface;
use ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class ConsultOnly extends \Magento\Framework\Model\AbstractModel
{

    protected $consultonlyDataFactory;

    protected $dataObjectHelper;

    //protected $_eventPrefix = 'consultonly_consultonlycollection_consultonly';
    protected $_eventPrefix = 'consultonly';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConsultOnlyInterfaceFactory $consultonlyDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly $resource
     * @param \ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ConsultOnlyInterfaceFactory $consultonlyDataFactory,
        DataObjectHelper $dataObjectHelper,
        \ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly $resource,
        \ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly\Collection $resourceCollection,
        array $data = []
    ) {
        $this->consultonlyDataFactory = $consultonlyDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve consultonly model with consultonly data
     * @return ConsultOnlyInterface
     */
    public function getDataModel()
    {
        $consultonlyData = $this->getData();
        
        $consultonlyDataObject = $this->consultonlyDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $consultonlyDataObject,
            $consultonlyData,
            ConsultOnlyInterface::class
        );
        
        return $consultonlyDataObject;
    }
}

