<?php
declare(strict_types=1);

namespace VirtualHub\UpdateContact\Model;

class UpdateContactManagement implements \VirtualHub\UpdateContact\Api\UpdateContactManagementInterface
{

    protected $orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function postUpdateContact($request)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];
        if(!is_array($request)){
            echo json_encode($response); die;
        }
        foreach ($request AS $requestItems){
            $incrementId    = $requestItems['order_id'];
            $mobile         = $requestItems['mobile'];
        }

        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        $shippingAddress = $order->getShippingAddress();
        $shippingAddress->setTelephone($mobile);
        $order->save();
        $message = "Mobile number updated successfully";
        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;

        try{
            $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
            $shippingAddress = $order->getShippingAddress();
            $shippingAddress->setTelephone($mobile);
            $order->save();
            $message = "Mobile number updated successfully";
            $response = ["success" => true, "message" => $message];
        } catch (\Exception $e){

        }
        echo json_encode($response); die;
    }
}

