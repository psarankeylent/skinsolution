<?php
declare(strict_types=1);

namespace VirtualHub\UpdateAddress\Observer\Backend\Admin;

class SalesOrderAddressUpdate implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->curl             = $curl;
        $this->configHelper     = $configHelper;
        $this->regionFactory    = $regionFactory;
        $this->productFactory   = $productFactory;
        $this->orderFactory     = $orderFactory;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/update_shipping_address.log');
        $logger = new \Zend\Log\Logger(); 
        $logger->addWriter($writer);

        $orderId    = $observer->getEvent()->getOrderId();
        $order      = $this->orderFactory->create()->load($orderId);

        $prescription   = false;
        foreach ($order->getAllItems() as $item){
            $productId =  $item->getProductId();
            $product = $this->productFactory->create()->load($productId);
            if($product->getPrescription()){
                $prescription = true;
            }
        }

        if($prescription){
            $shippingAddress                  = $order->getShippingAddress();
            $request['magento_order_id']      = $order->getIncrementId();
            $request['order_address_1']       = $shippingAddress->getStreet()[0];
            $request['order_address_2']       = isset($shippingAddress->getStreet()[1]) ? $shippingAddress->getStreet()[1] : '';
            $request['order_city']            = $shippingAddress->getCity();
            $request['order_zipcode']         = $shippingAddress->getPostcode();
            $request['order_address_type']    = $shippingAddress->getAddressType();
            $request['order_state']           = $shippingAddress->getRegion();
            $regionId                         = $shippingAddress->getRegionId();
            $request['order_state_id']        = $this->regionFactory->create()->load($regionId)->getCode();
            $request['order_patient_phone']   = $shippingAddress->getTelephone();
            $request['magento_version']       = "SSMD2";
            //$logger->info($request);

            $bearerToken = $this->configHelper->getVirtualHubBearerToken();
            if($bearerToken['success'] == True){
                $token = $bearerToken['token'];
                $updateAddress = $this->configHelper->getOrderAddressUpdateApi();
                $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer '.$token];
                $this->curl->setHeaders($headers);
                $this->curl->post($updateAddress, json_encode($request));
                $response = $this->curl->getBody();
                //$response = json_decode($response, true);
                $logger->info(json_encode($request));
                $logger->info($response);
            }
        }
    }
}

