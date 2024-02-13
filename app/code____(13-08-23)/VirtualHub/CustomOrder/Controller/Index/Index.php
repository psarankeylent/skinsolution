<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace VirtualHub\CustomOrder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Customer\Model\Customer $customer,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

        $this->curl = $curl;
        $this->customer = $customer;
        $this->orderFactory     = $orderFactory;
        $this->configHelper     = $configHelper;
        $this->productFactory   = $productFactory;
        $this->regionFactory    = $regionFactory;
        $this->prescriptionsFactory = $prescriptionsFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_order_to_vh.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$order = $observer->getEvent()->getOrder();
        $order      = $this->orderFactory->create()->load(1361219);

        $request['order_consult_type']    = 'SF';
        $request['magento_order_id']      = $order->getIncrementId();
        $request['order_grand_total']     = $order->getGrandTotal();
        $request['order_create_date']     = $order->getCreatedAt();
        $request['reapprove']             = '';

        $prescription   = false;
        $orderType      = false;
        $itemsData      = [];
        foreach ($order->getAllItems() as $item){
            $row = [];
            $productId =  $item->getProductId();
            $product = $this->productFactory->create()->load($productId);
            if($product->getPrescription()){
                //$row['vh_prescription_id'] = $product->getPrescription();
                $row['order_item_id']   = $item->getId();
                $row['order_item_sku']  = $item->getSku();
                $row['order_item_name'] = $item->getName();
                $row['order_item_qty']  = $item->getQtyOrdered();
                $row['order_item_purchase_type'] = '';

                $Prescriptions = $this->prescriptionsFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("id", $product->getPrescription())
                    ->getFirstItem();

                $row['vh_prescription_id'] = $Prescriptions->getData('vh_prescription_id');

                $ndcLatisse = "latisse";
                $ndcObagi = "Obagi";
                $ndcValue = "00000000000";

                if(strpos( strtolower($item->getName()), $ndcLatisse ) !== false) {
                    $ndcValue = "00023361605";
                }else if(strpos( strtolower($item->getName()), $ndcObagi ) !== false){
                    $ndcValue = "63402030230";
                }
                $row['order_item_ndc']  = $ndcValue;
                $itemsData[] = $row;
                $prescription = true;
            }
            if(!$prescription){
                $orderType = true;
            }
        }
        $request['order_items_arr'] = $itemsData;

        if($orderType)
            $request['order_type'] = 'mixed';
        else
            $request['order_type'] = 'rx-only';

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

        //return $this->resultPageFactory->create();
    }
}

