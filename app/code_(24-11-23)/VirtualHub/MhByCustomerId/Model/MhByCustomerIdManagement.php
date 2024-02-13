<?php

declare(strict_types=1);

namespace VirtualHub\MhByCustomerId\Model;

class MhByCustomerIdManagement implements \VirtualHub\MhByCustomerId\Api\MhByCustomerIdManagementInterface
{
    public function __construct(
        \Backend\Medical\Model\MedicalFactory $medicalFactory
    ) {
        $this->medicalFactory = $medicalFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function getMhByCustomerId($customerId)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "customerId" => $customerId];        

        if(!empty($customerId)){
            $medicalFactory = $this->medicalFactory->create()
                ->getCollection()
                ->addFieldToFilter("customer_id", $customerId);

            if (!empty($medicalFactory->getData()) && is_array($medicalFactory->getData())) {
                $response = ["success" => true, "customerId" => $customerId, "mhByCustomerId"=>$medicalFactory->getData()];
            }
        }
        echo json_encode($response); die;
    }
}

