<?php

declare(strict_types=1);

namespace VirtualHub\Medicalinfo\Model;

class MedicalinfoManagement implements \VirtualHub\Medicalinfo\Api\MedicalinfoManagementInterface
{
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \MH\Tables\Model\MhByOrdersFactory $mhByOrdersFactory
    ) {
        $this->orderFactory     = $orderFactory;
        $this->medicalFactory   = $medicalFactory;
        $this->mhByOrdersFactory   = $mhByOrdersFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedicalinfo($incrementId)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "incrementId" => $incrementId];

        if(!empty($incrementId)){
            $mhByOrdersCollection = $this->mhByOrdersFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter("increment_id", $incrementId);
            /*
            $customerMedicalHistoryData = $this->customerMedicalHistoryFactory->create()
                ->getCollection()
                ->addFieldToSelect(array('question_text', 'response','customer_id','question_id'))
                ->addFieldToFilter("order_number", $incrementId);
            */

            if (!empty($mhByOrdersCollection->getData()) && is_array($mhByOrdersCollection->getData())) {
                $response = ["success" => true, "incrementId" => $incrementId, "medicalinfo" => $mhByOrdersCollection->getData()];
            }else{

                $order      = $this->orderFactory->create()->loadByIncrementId($incrementId);
                $customerId = $order->getCustomerId();

                if($customerId){
                    /*
                    $customerMedicalHistoryData = $this->customerMedicalHistoryFactory->create()
                        ->getCollection()
                        ->addFieldToFilter("customer_id", $customerId)
                        ->setOrder('order_number','DESC')
                        ->getFirstItem();
                    */
                        $mhCollections = $this->medicalFactory->create()
                            ->getCollection()
                            ->addFieldToFilter("customer_id", $customerId);

                    if (!empty($mhCollections->getData()) && is_array($mhCollections->getData())) {

                        $mhByOrders = $this->mhByOrdersFactory->create();

                        foreach ($mhCollections AS $mhCollection) {
                            $data = array('increment_id' => $incrementId,'customer_id' => $mhCollection->getData('customer_id'), 'question_id' => $mhCollection->getData('question_id'), 'question_text' => $mhCollection->getData('question_text'), 'response' => $mhCollection->getData('response'), 'updated_at'=> date('Y-m-d H:i:s'));

                            if($mhCollection->getData('id')){
                               $mhByOrders->setData($data);
                               $mhByOrders->save();
                            }
                        }
                        $response = ["success" => true, "customerId" => $customerId, "incrementId" => $incrementId, "medicalinfo" => $mhCollections->getData()];
                    }
      
                }

            }
        }
        echo json_encode($response); die;
    }
}


