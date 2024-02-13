<?php
declare(strict_types=1);

namespace VirtualHub\MedicalPhoto\Model;

class MedicalPhotoManagement implements \VirtualHub\MedicalPhoto\Api\MedicalPhotoManagementInterface
{
    protected $customerPhotosFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory
    ) {
        $this->orderFactory     = $orderFactory;
        $this->customerPhotosFactory = $customerPhotosFactory;
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function medicalPhotos($incrementId)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "incrementId" => $incrementId];
        
        if(!empty($incrementId)){
            $order      = $this->orderFactory->create()->loadByIncrementId($incrementId);
            $customerId = $order->getCustomerId();

            if($customerId){
                $photosCollection = $this->customerPhotosFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", 1);

                if (!empty($photosCollection->getData()) && is_array($photosCollection->getData())) {
                    $response = ["success" => true, "incrementId" => $incrementId, "photos"=>$photosCollection->getData()];
                }

            }





            /*
            $medicalHistoryCollection = $this->customerMedicalHistoryFactory->create()->getCollection();
            $medicalHistoryCollection->addFieldToFilter('order_number', $incrementId);
            $medicalHistoryCollectionFirstItem = $medicalHistoryCollection->getFirstItem();
            $medicalHistoryPhotosArray = $medicalHistoryCollectionFirstItem->getData();
            if (!empty($medicalHistoryPhotosArray) && is_array($medicalHistoryPhotosArray)) {
                $fullFace = $medicalHistoryPhotosArray['full_face'];
                $govtId = $medicalHistoryPhotosArray['govt_id'];

                $customerPhotosCollection = $this->customerPhotosFactory->create()
                                                ->getCollection()
                                                ->addFieldToSelect(array('photo_type', 'path'))
                                                ->addFieldToFilter('photo_id', array('in' => array($fullFace, $govtId)));

                if (!empty($customerPhotosCollection->getData()) && is_array($customerPhotosCollection->getData())) {
                    $response = ["success" => true, "incrementId" => $incrementId, "photos"=>$customerPhotosCollection->getData()];
                }
            }*/



        }

        echo json_encode($response); die;
    }
}



