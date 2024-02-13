<?php
declare(strict_types=1);

namespace VirtualHub\OrderCancellation\Observer\Backend\Order;

class CancelAfter implements \Magento\Framework\Event\ObserverInterface
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

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order_cancellation.log');
        $logger = new \Zend\Log\Logger(); 
        $logger->addWriter($writer);

        $order      = $observer->getEvent()->getOrder();
        $incrementId = $order->getIncrementId();
        $order      = $this->orderFactory->create()->loadByIncrementId($incrementId);

        $prescription   = false;
        foreach ($order->getAllItems() as $item){
            $productId =  $item->getProductId();
            $product = $this->productFactory->create()->load($productId);
            if($product->getPrescription()){
                $prescription = true;
            }
        }

        if($prescription){
            $request['order_id'] = $order->getIncrementId();
            $request['mg_order_id']     = $order->getIncrementId();
            
            $bearerToken = $this->configHelper->getVirtualHubBearerToken();
            if($bearerToken['success'] == True){
                $token = $bearerToken['token'];
                $orderCancellation = $this->configHelper->getOrderCancellationApiUrl();
                $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer '.$token];
                $this->curl->setHeaders($headers);
                $this->curl->post($orderCancellation, json_encode($request));
                $response = $this->curl->getBody();
                //$response = json_decode($response, true);
                $logger->info(json_encode($request));
                $logger->info($response);
            }
        }

    }
}

