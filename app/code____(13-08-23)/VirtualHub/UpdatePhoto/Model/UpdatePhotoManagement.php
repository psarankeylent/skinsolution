<?php

declare(strict_types=1);

namespace VirtualHub\UpdatePhoto\Model;

class UpdatePhotoManagement implements \VirtualHub\UpdatePhoto\Api\UpdatePhotoManagementInterface
{

    public function __construct(
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory
    ) {
        $this->customerPhotosFactory = $customerPhotosFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePhoto($request)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];

        if(!is_array($request)){
            echo json_encode($response); die;
        }

        foreach ($request AS $requestItems){
            $customerId = $requestItems['customer_id'];
            $photoType   = $requestItems['photo_type'];
            $photoName   = $requestItems['photo_name'];
            $path   = $requestItems['path'];
        }
        
        $collection = $this->customerPhotosFactory->create()
            ->getCollection()
            ->addFieldToFilter("customer_id", $customerId)
            ->addFieldToFilter("photo_type", $photoType)
            ->addFieldToFilter("status", 1)
            ->getFirstItem();

        if($collection->getData('photo_id')){
           $collection->addData(array('status' => 0));
           $collection->save();
           //$message = "Prescriptions updated successfully";
        }
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/update_medical_photos.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($request);
        
        //$path = implode('/', str_split($customerId)).$customerId.'_'.$photoType.'_'.$photoName;
        $data = array('customer_id' => $customerId, 'photo_type' => $photoType, 'path' => $path, 'status' => 1,'source_system' => 'VirtualHub','created_at' => date('Y-m-d h:i:s'));

        $collection->setData($data);
        $collection->save();

        $message = "Photo added successfully";

        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;

    }
}


