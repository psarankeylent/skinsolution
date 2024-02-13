<?php

declare(strict_types=1);

namespace ConsultOnly\UpdateConsultOnly\Model;

class ConsultOnlyInfoManagement implements \ConsultOnly\UpdateConsultOnly\Api\ConsultOnlyInfoManagementInterface
{
    protected $customerPhotosFactory;
    protected $customerMedicalHistoryFactory;

    public function __construct(
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory
    ) {
        $this->customerPhotosFactory        = $customerPhotosFactory;
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function getConsultOnlyInfo($customerId)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "customerId" => $customerId];

        if(!empty($customerId)){
            $uniqueIdCollection = $this->customerMedicalHistoryFactory->create()
                ->getCollection()
                ->addFieldToSelect(array('question_text', 'response','customer_id','question_id','unique_id'))
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter('unique_id', array('like' => 'consult%'))
                ->setOrder('created_at','DESC');
            $uniqueIdCollection->getSelect()->limit(1);

            if($uniqueIdCollection->count()>0){
                foreach ($uniqueIdCollection AS $uniqueIdCollectionValue) {
                    $uniqueId = $uniqueIdCollectionValue->getUniqueId();
                }

                $customerMedicalHistoryData = $this->customerMedicalHistoryFactory->create()
                    ->getCollection()
                    ->addFieldToSelect(array('question_text', 'response','question_id','customer_id','created_at'))
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("unique_id", $uniqueId);

                $customerPhotos = $this->customerPhotosFactory->create()
                    ->getCollection()
                    ->addFieldToSelect(array('photo_type', 'path'))
                    ->addFieldToFilter("status", 1)
                    ->addFieldToFilter("customer_id", $customerId);
                

                if (!empty($customerMedicalHistoryData->getData()) && is_array($customerMedicalHistoryData->getData())) {
                    $response = ["success" => true, "customerId" => $customerId, "photos" => $customerPhotos->getData(), "medicalinfo" => $customerMedicalHistoryData->getData()];
                }

            }
        }
        echo json_encode($response); die;
    }
}

