<?php

declare(strict_types=1);

namespace VirtualHub\PhotoByCustomerId\Model;

class PhotoByCustomerIdManagement implements \VirtualHub\PhotoByCustomerId\Api\PhotoByCustomerIdManagementInterface
{

    public function __construct(
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory
    ) {
        $this->customerPhotosFactory = $customerPhotosFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhotoByCustomerId($customerId)
    {

        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false, "customerId" => $customerId];        

        if(!empty($customerId)){
            $customerPhotosCollection = $this->customerPhotosFactory->create()
                ->getCollection()
                ->addFieldToFilter("customer_id", $customerId)
                //->addFieldToFilter("photo_type", $photoType)
                ->addFieldToFilter("status", 1);
                //->getFirstItem();

            //$response = ["success" => true, "message" => $message];
            //echo json_encode($response); die;

            if (!empty($customerPhotosCollection->getData()) && is_array($customerPhotosCollection->getData())) {
                $response = ["success" => true, "customerId" => $customerId, "photos"=>$customerPhotosCollection->getData()];
            }
        }
        echo json_encode($response); die;

    }
}

