<?php
declare(strict_types=1);

namespace VirtualHub\UpdateDateOfBirth\Model;

class UpdateDateOfBirthManagement implements \VirtualHub\UpdateDateOfBirth\Api\UpdateDateOfBirthManagementInterface
{

    protected $customerMedicalHistoryFactory;

    public function __construct(
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory
    ) {
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdateDateOfBirth($request)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];
        if(!is_array($request)){
            echo json_encode($response); die;
        }
        foreach ($request AS $requestItems){
            $incrementId    = $requestItems['order_id'];
            $questionId     = $requestItems['question_id'];
            $dateOfBirth    = $requestItems['date_of_birth'];
        }
        $dateOfBirthData = array('response' => $dateOfBirth);
        $customerMedicalHistory = $this->customerMedicalHistoryFactory->create()
            ->getCollection()
            ->addFieldToFilter("order_number", $incrementId)
            ->addFieldToFilter("question_id", $questionId)
            ->getFirstItem();

        //echo json_encode($customerMedicalHistory->getData()); die;
        if($customerMedicalHistory->getData('id')){
           $customerMedicalHistory->addData($dateOfBirthData);
           $customerMedicalHistory->save();
           $message = "Date Of Birth updated successfully";
        }

        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;
    }
}
