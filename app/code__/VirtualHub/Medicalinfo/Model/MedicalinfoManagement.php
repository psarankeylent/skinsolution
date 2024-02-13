<?php

declare(strict_types=1);

namespace VirtualHub\Medicalinfo\Model;

class MedicalinfoManagement implements \VirtualHub\Medicalinfo\Api\MedicalinfoManagementInterface
{
    protected $customerMedicalHistoryFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory
    ) {
        $this->orderFactory     = $orderFactory;
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedicalinfo($incrementId)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "incrementId" => $incrementId];

        if(!empty($incrementId)){
            $customerMedicalHistoryData = $this->customerMedicalHistoryFactory->create()
                ->getCollection()
                ->addFieldToSelect(array('question_text', 'response','customer_id','question_id'))
                ->addFieldToFilter("order_number", $incrementId);

            if (!empty($customerMedicalHistoryData->getData()) && is_array($customerMedicalHistoryData->getData())) {
                $response = ["success" => true, "incrementId" => $incrementId, "medicalinfo" => $customerMedicalHistoryData->getData()];
            }else{

                $order      = $this->orderFactory->create()->loadByIncrementId($incrementId);
                $customerId = $order->getCustomerId();

                if($customerId){
                    $customerMedicalHistoryData = $this->customerMedicalHistoryFactory->create()
                        ->getCollection()
                        ->addFieldToFilter("customer_id", $customerId)
                        ->setOrder('order_number','DESC')
                        ->getFirstItem();

                    if (!empty($customerMedicalHistoryData->getData()) && is_array($customerMedicalHistoryData->getData())) {

                        $tmpIncrementId         = $customerMedicalHistoryData->getData('order_number');
                        $customerMedicalHistory = $this->customerMedicalHistoryFactory->create()
                            ->getCollection()
                            ->addFieldToSelect(array('question_text', 'response','customer_id','question_id'))
                            ->addFieldToFilter("order_number", $tmpIncrementId);

                        foreach ($customerMedicalHistory AS $medicalHistory) {
                            $data = array('order_number'=>$incrementId,'customer_id'=>$customerId,'question_id'=>$medicalHistory->getQuestionId(),'question_text'=>$medicalHistory->getQuestionText(),'response'=>$medicalHistory->getResponse());
                            $medicalHistory->setData($data);
                            $medicalHistory->save();
                        }

                        $response = ["success" => true, "customerId" => $customerId, "incrementId" => $incrementId, "medicalinfo" => $customerMedicalHistory->getData()];

                    }
                        
                }

            }
        }
        echo json_encode($response); die;
    }
}

