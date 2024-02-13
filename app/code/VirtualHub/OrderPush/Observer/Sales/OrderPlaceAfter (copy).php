<?php

declare(strict_types=1);

namespace VirtualHub\OrderPush\Observer\Sales;

// Email notification
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

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
        \VirtualHub\VhOrderLog\Model\VhOrderLogFactory  $vhOrderLogFactory,
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Ssmd\StoreCredit\Model\QuoteStorecreditFactory $qscFactory
        
    ) {
        
        $this->curl             = $curl;
        $this->customer         = $customer;
        $this->configHelper     = $configHelper;
        $this->productFactory   = $productFactory;
        $this->regionFactory    = $regionFactory;
        $this->vhOrderLogFactory = $vhOrderLogFactory;
        $this->prescriptionsFactory = $prescriptionsFactory;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->templateFactory = $templateFactory;
        $this->medicalFactory = $medicalFactory;
        $this->qscFactory = $qscFactory;
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
                $orderType = true;
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
                $responseArray = json_decode($response, true);

                //$logger->info('responseArray');
                //$logger->info($responseArray);

                if($responseArray['success']){
                    $success = "success";
                }else{
                    $success = "error";
                }

                $msg = $responseArray['msg']; 
                $savedata = array('response_status' => $success, 'response_msg' => $msg, 'magento_order_id' => $order->getIncrementId(), 'order_grand_total' => $order->getGrandTotal(),'order_items_arr' => json_encode($itemsData),'order_type' => $request['order_type'],'order_address_1' => $request['order_address_1'],'order_address_2' => $request['order_address_2'],'order_city' => $request['order_city'],'order_state' => $request['order_state'],'order_state_id' => $request['order_state_id'],'order_patient_phone' => $request['order_patient_phone'],'order_patient_fname' => $request['order_patient_fname'],'order_patient_lname' => $request['order_patient_lname'],'order_patient_id' => $request['order_patient_id'],'order_patient_email' => $request['order_patient_email'], 'order_patient_gender' => $request['order_patient_gender'], 'response_json' => $response);

                $vhOrderLog = $this->vhOrderLogFactory->create();
                $vhOrderLog->setData($savedata);
                $vhOrderLog->save();

            }
            
        } // End prescription IF

        // ============ Store credit usage Notification Email by hitesh start (6-12-23) =====================
            $this->sendStoreCreditUsageEmail($order->getQuoteId(), $order->getCustomerId());
        // ============ Store credit usage Notification Email by hitesh end (6-12-23) =======================
    }

    public function sendStoreCreditUsageEmail($quoteId, $customerId)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/storecredit_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $quoteStoreCreditCollection = $this->qscFactory->create()->getCollection();
        $quoteStoreCreditCollection->addFieldToFilter('quote_id', $quoteId);

        if(!empty($quoteStoreCreditCollection->getData()))
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($customerId);

            $templateId = 1000052;           
            $name = $customer->getFirstname().' '.$customer->getLastname();
            $customer_email = $customer->getEmail();
            $amount=0;
            $amount = (float)$quoteStoreCreditCollection->getFirstItem()->getData()['applied_storecredit'];

            // ================ Send email code start ===============
            $this->inlineTranslation->suspend();
            $sender = [
                'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'customer_name'    => $name,
                    'amount_used'      => $amount
                ])
                ->setFrom($sender)
                ->addTo($customer_email)
                ->getTransport();

            
            try {
                $transport->sendMessage();
                
                // Text Message getting
                $templateObject    = $this->templateFactory->create()->load($templateId);
                $emailTextMessage  = $templateObject->getTemplateText();

                $trackLog = $this->creditCardExpiringModelFactory->create();
                $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Store Credit Usage', 'email_message' => $emailTextMessage);
                $trackLog->setData($dataToSave);
                $trackLog->save();

                $logger->info('Store credit usage email sent successfully.');


            } catch (\Exception $e) {
                $trackLog = $this->creditCardExpiringModelFactory->create();
                $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Store Credit Usage','email_message' => $e->getMessage());
                $trackLog->setData($dataToSave);
                $trackLog->save();

                $logger->info('Error while sending store credit usage email '.$e->getMessage());
            }
            $this->inlineTranslation->resume();


        }
        else
        {
            $logger->info('In "quote_storecredit" table, no quote ID found '. "QuoteID ".$quoteId);
        }
        

    }

}


