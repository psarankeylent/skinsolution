<?php
declare(strict_types=1);

namespace VirtualHub\UpdateDateOfBirth\Model;

class UpdateDateOfBirthManagement implements \VirtualHub\UpdateDateOfBirth\Api\UpdateDateOfBirthManagementInterface
{

    protected $customerMedicalHistoryFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \MH\Tables\Model\MhByOrdersFactory $mhByOrdersFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory
    ) {
        $this->orderFactory     = $orderFactory;
        $this->medicalFactory   = $medicalFactory;
        $this->mhByOrdersFactory   = $mhByOrdersFactory;
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdateDateOfBirth($request)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dob.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($request);
                
        if(!is_array($request)){
            echo json_encode($response); die;
        }
        $incrementId    = '';
        foreach ($request AS $requestItems){
            $incrementId    = $requestItems['order_id'];
            $questionId     = $requestItems['question_id'];
            $dateOfBirth    = $requestItems['date_of_birth'];
        }

        if(!empty($incrementId)){
            $order      = $this->orderFactory->create()->loadByIncrementId($incrementId);
            $customerId = $order->getCustomerId();

            if(!empty($customerId)){
                $mhCollections = $this->medicalFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("question_id", $questionId)
                    ->getFirstItem();

                if($mhCollections->getData('id')){
                   $dateOfBirthData = array('response' => $dateOfBirth);
                   $mhCollections->addData($dateOfBirthData);
                   $mhCollections->save();
                   $message = "Date Of Birth updated successfully";
                }
            }
        }

        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;

    }
}


