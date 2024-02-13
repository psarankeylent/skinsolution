<?php

declare(strict_types=1);

namespace Backend\Medical\Model;

use Backend\Medical\Api\Data\MedicalInterface;
use Backend\Medical\Api\Data\MedicalInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Medical extends \Magento\Framework\Model\AbstractModel
{

    protected $medicalDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'medical_history';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param MedicalInterfaceFactory $medicalDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Backend\Medical\Model\ResourceModel\Medical $resource
     * @param \Backend\Medical\Model\ResourceModel\Medical\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        MedicalInterfaceFactory $medicalDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Backend\Medical\Model\ResourceModel\Medical $resource,
        \Backend\Medical\Model\ResourceModel\Medical\Collection $resourceCollection,
        array $data = []
    ) {
        $this->medicalDataFactory = $medicalDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve medical model with medical data
     * @return MedicalInterface
     */
    public function getDataModel()
    {
        $medicalData = $this->getData();
        
        $medicalDataObject = $this->medicalDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $medicalDataObject,
            $medicalData,
            MedicalInterface::class
        );
        
        return $medicalDataObject;
    }
}

