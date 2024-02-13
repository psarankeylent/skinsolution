<?php

declare(strict_types=1);

namespace VirtualHub\OrderPush\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Customer\Model\Customer $customer,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory
    ) {
        $this->curl = $curl;
        $this->customer = $customer;
        $this->configHelper = $configHelper;
        $this->productFactory = $productFactory;
        $this->regionFactory = $regionFactory;
        $this->prescriptionsFactory = $prescriptionsFactory;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order_request.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $order = $observer->getEvent()->getOrder();

        $request['order_consult_type']    = 'SF';
        $request['magento_order_id']      = $order->getIncrementId();
        $request['order_grand_total']     = $order->getGrandTotal();
        $request['order_create_date']     = $order->getCreatedAt();
        $request['reapprove']             = '';

        $prescription   = false;
        $orderType      = false;
        $itemsData      = [];
        $prescriptionCollection = '';
        foreach ($order->getAllItems() as $item){
            $row = [];
            $productId =  $item->getProductId();
            $product = $this->productFactory->create()->load($productId);
            $logger->info('Line 57 = '.$product->getPrescription());
            if($product->getPrescription()){

                $row['order_item_id']   = $item->getId();
                $row['order_item_sku']  = $item->getSku();
                $row['order_item_name'] = $item->getName();
                $row['order_item_qty']  = $item->getQtyOrdered();
                $row['order_item_purchase_type'] = '';

                $prescriptionCollection = $this->prescriptionsFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("id", $product->getPrescription())
                    ->getFirstItem();

                $row['vh_prescription_id'] = $prescriptionCollection->getData('vh_prescription_id');

                $itemsData[] = $row;
                $prescription = true;

            }
            if(!$product->getPrescription()){
                $logger->info('Line 75 = '.$orderType);
                $orderType = true;
                $logger->info('Line 77 = '.$orderType);
            }

        }
        $request['order_items_arr'] = $itemsData;

        if($orderType)
            $request['order_type'] = 'mixed';
        else
            $request['order_type'] = 'rx-only';

        //$logger->info('Line 91 = ');
        //$logger->info($request);

        if($prescription){
            //Address
            $shippingAddress                  = $order->getShippingAddress();     
            $request['order_address_1']       = $shippingAddress->getStreet()[0];
            $request['order_address_2']       = isset($shippingAddress->getStreet()[1]) ? $shippingAddress->getStreet()[1] : '';
            $request['order_city']            = $shippingAddress->getCity();
            $request['order_zipcode']         = $shippingAddress->getPostcode();
            $request['order_address_type']    = $shippingAddress->getAddressType();
            $request['order_state']           = $shippingAddress->getRegion();
            $regionId                         = $shippingAddress->getRegionId();
            $request['order_state_id']        = $this->regionFactory->create()->load($regionId)->getCode();
            $request['order_patient_phone']   = $shippingAddress->getTelephone();

            //Customer Data
            $request['order_patient_fname']   = $order->getCustomerFirstname();
            $request['order_patient_lname']   = $order->getCustomerLastname();
            $request['order_patient_medications']     = '';
            $request['order_patient_allergies']   = '';

            $customer                         = $this->customer->load($order->getCustomerId());
            $request['order_patient_id']      = $order->getCustomerId();
            $request['order_patient_email']   = $order->getCustomerEmail();
            $request['order_patient_dob']     = $customer->getDob();
            $request['order_patient_gender']  = $customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getGender());
            $logger->info($request);
            
            $bearerToken = $this->configHelper->getVirtualHubBearerToken();
            if($bearerToken['success'] == True){
                $token = $bearerToken['token'];
                $orderSyncApiUrl = $this->configHelper->getOrderSyncApiUrl();
                $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer '.$token];
                $this->curl->setHeaders($headers);
                $this->curl->post($orderSyncApiUrl, json_encode($request));
                $response = $this->curl->getBody();
                //$response = json_decode($response, true);
                $logger->info('response');
                $logger->info($response);
            }
            
        } // End prescription IF

    }

}

