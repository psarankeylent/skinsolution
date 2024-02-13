<?php

namespace CustomOrders\GenerateOrders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
 
class Data extends AbstractHelper
{
    protected $messageManager;
    protected $storeManager;
    protected $customerSession;
    protected $quoteRepository;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $storeRepository;
    protected $productFactory;
    protected $productRepository;
    protected $formkey;
    protected $quote;
    protected $quoteManagement;
    protected $customerFactory;
    protected $customerRepository;
    protected $orderService;
    protected $emulation;
    protected $cartItemInterface;
    protected $catalogSession;
    protected $option;
    protected $value;
    protected $session;
    protected $eventManager;
    protected $card;
    protected $cardRepository;
    protected $addressCollection;
    protected $generateOrderFactory;
    protected $scopeInterface;
    protected $orderFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Quote\Api\Data\CartItemInterface $cartItemInterface,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Catalog\Model\Product\Option\Value $value,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \ParadoxLabs\TokenBase\Model\Card $card,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection,
        \CustomOrders\GenerateOrders\Model\GenerateOrderFactory $generateOrderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeRepository = $storeRepository;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->emulation = $emulation;
        $this->cartItemInterface = $cartItemInterface;
        $this->catalogSession = $catalogSession;
        $this->option = $option;
        $this->value = $value;
        $this->session = $session;
        $this->eventManager = $eventManager;
        $this->card = $card;
        $this->cardRepository = $cardRepository;
        $this->addressCollection = $addressCollection;
        $this->generateOrderFactory = $generateOrderFactory;
        $this->scopeInterface = $scopeInterface;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }
 
    public function createCustomOrder($orderDetailsArr)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "custom_orders_report.log file"'];

        
         
        // Store Info Setting
        $store = $this->storeRepository->get('default');              
        $this->emulation->startEnvironmentEmulation($store->getId(), \Magento\Framework\App\Area::AREA_FRONTEND);
        $this->storeManager->getStore()->setWebsiteId($store->getWebsiteId());

        $orderData = $this->getOrderByIncrementId($orderDetailsArr['reference_increment_id']);
        $oldOrder = $orderData[0];
        //echo "<pre>"; print_r($oldOrder); exit;

        // Customer Creation Function
        $customer = $this->saveCustomer($store, $oldOrder);

        // Quote Creation Function
        $quote = $this->createQuote(
                        $store,
                        $orderDetailsArr['sku'],
                        $orderDetailsArr['sku_option_id'],
                        $orderDetailsArr['sku_option_type_id'],
                        $oldOrder,
                        $customer
                );
        
        // Create Order Function
        $res = $this->createOrder($quote, $orderDetailsArr['id']);
        //  echo "<pre>"; print_r($res); exit;

        if($res['error'] == 0 && $res['profile_id'] != "" && $res['payment_id'] != "" )
        {                
            $orders_created[] = $res['order_id']; 
            // Customer Charge via Authorizenet APi
            $authApiResponse = $this->chargeCustomerProfile($res['profile_id'], $res['payment_id'], $res['grand_total']);

        
           // echo "<pre>"; print_r($authApiResponse); exit;
            if($authApiResponse['trans_id'] != "")
            {
                $authResponse['authorize_Response'] = $authApiResponse;


                // Create Invoice                   
                $invoice = $this->createInvoice($optionId, $optionTypeId, $res['order_id'],$res['profile_id'],$res['payment_id'],$authApiResponse['trans_id'], $transcode ='consOrder');


                // Save prescription data and Send order to zeelify                   
                $savePresObj = $objectManager->get('Enhance\Prescriptions\Model\SavePrescriptions');            
                
                $savePresObj->processPrescriptionsFromOrderIncId($res['order_id'], $is_consult_only=1, $uniqueRespId);  // Save prescription from order incr id

                $addDataToShipStationLog = $objectManager->create('Enhance\ShipStation\Helper\AddDataToShipStationLog');
                $addDataToShipStationLog->processData($res['order_id']); 

            }
            else
            {

                $orders_created = [
                        'error' => 1,
                        'trans_id' => $authApiResponse['trans_id'],
                        'error_code' => $authApiResponse['error_code'],
                        'trans_message' => $authApiResponse['trans_message']
                ];

                $authResponse['error'] = 1;
                $authResponse['trans_id'] = $authApiResponse['trans_id'];
                $authResponse['error_code'] = $authApiResponse['error_code'];
                $authResponse['trans_message'] = $authApiResponse['trans_message'];

                $authResponse['authorize_Response'] = $authApiResponse;
            }
            
        }

        return $result;
    }

    // Return $order as an array with Search CriteriaBuilder
    public function getOrderByIncrementIdWithCriteria($incrementId) {

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId)->create();
        $orderData = null;

        try {
            $orderData = $this->orderRepository->getList($searchCriteria)->getData();            
        }catch(\Exception $e){
            echo $e->getMessage(); 
        }        
        return $orderData; 
    }
    // Return $order as an object
    public function getOrderByIncrementId($incrementId) {

        $orderData = array();
        try {
           $collection = $this->orderFactory->create()->getCollection();
                      $collection->addFieldToFilter('increment_id', array('increment_id' => $incrementId));

            $orderData = $collection->getData();

        }catch(\Exception $e){
            echo $e->getMessage(); 
        }        
        return $orderData; 
    }

    // Return Customer Object
    public function saveCustomer($store, $order)
    {
        $shippingAddress = $this->getShippingAddress($order['entity_id']);
        $email = $order['customer_email'];

        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($store->getWebsiteId());
        $customer->loadByEmail($email); // load customet by email address
        if (!$customer->getEntityId()) {
            //If not avilable then create this customer
           $customer->setWebsiteId($store->getWebsiteId())
                    ->setStore($store)
                    ->setFirstname($shippingAddress['firstname'])
                    ->setLastname($shippingAddress['lastname'])
                    ->setEmail($email)
                    ->save();
        }

        return $customer;

    }
    /* get Shipping address data of specific order */
    public function getShippingAddress($orderId) {
        $order = $this->orderRepository->get($orderId);
        /* check order is not virtual */
        if(!$order->getIsVirtual()) {
            $orderShippingId = $order->getShippingAddressId();
            $address = $this->addressCollection->create()->addFieldToFilter('entity_id',array($orderShippingId))->getFirstItem();
            return $address->getData();
        }
        return null;
    }

    /* get Billing address data of specific order */
    public function getBillingAddress($orderId) {
        $order = $this->orderRepository->get($orderId);
        $orderBillingId = $order->getBillingAddressId();
        $address = $this->addressCollection->create()->addFieldToFilter('entity_id',array($orderBillingId))->getFirstItem();
        return $address->getData();
    }

    // Return \Magento\Framework\Model\Quote Object
    public function createQuote($store, $sku, $optionId, $optionTypeId, $oldOrder,$customer)
    {
        $billingAddress  = $this->getBillingAddress($oldOrder['entity_id']);
        $shippingAddress = $this->getShippingAddress($oldOrder['entity_id']);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            $quote = $this->quote->create(); // Create Quote Object
            $quote->setStore($store); // Set Store
            $customer = $this->customerRepository->getById($customer->getEntityId());
            $quote->setCurrency();
            $quote->assignCustomer($customer); // Assign quote to Customer
           
     
            //add items in quote
            // Set data in quote_items table.
            $discountAmount=0;
            $discountPercent=0;
            $taxAmount=0;
            $taxPercent=0;
            $subTotal=0;
            $itemsQty=0;
            $optionsArr = [];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $options = [];
            $logger->info('Product sku - '.$sku);

            try {
               $product = $this->productRepository->get($sku);
            } catch (\Exception $e) {
                //$logger->info('product is not available '.$e->getMessage());
                $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "custom_orders_report.log file"'];
                throw new \Exception($e->getMessage());                    
            }
            
            $price = 99;
            $product->setPrice($price);


            // ========================= Product Added To Cart(With Custom option if any) ===================

            // Product Custom options If any
            $params = [];
            if( isset($optionId) && isset($optionTypeId) )
            {
                $options[$optionId] = $optionTypeId;
                $params['options'] = $options;

            }
            $params['product'] = $product->getId();
            $params['qty'] = 1;
            //echo "<pre>"; print_r($params); exit;

            $this->saveProductToCart($product, $params);

            
            // ============================= Set Product Item Data ====================================

            $this->saveQuoteItems($quote, $product, $price, $discountAmount,$discountPercent,$taxAmount, $taxPercent);

            $option = $objectManager->get('Magento\Catalog\Model\Product\Option')->load($optionId);

            $optionTitle = $option->getTitle();
            $optionType = $option->getType();
            
            $value = $objectManager->get('Magento\Catalog\Model\Product\Option\Value')->load($option->getId());
            $valueTitle = $value->getTitle();

            $subscriptionType = 'Onetime';
            $productId = $product->getId();
             
            // ItemsQty & ItemsCount
            $itemsQty = 1;
            $itemsCount = 1;
        
            $grandTotal = (($subTotal-$discountAmount)+$taxAmount);    
            $quote->setItemsQty($itemsQty)
                  ->setItemsCount($itemsCount);
                 
            $quote->setSubtotal($subTotal)
                  ->setBaseSubtotal($subTotal)
                  ->setBaseGrandTotal($grandTotal)
                  ->setGrandTotal($grandTotal);

            // Set Billing & Shiping Address
            $quote->getBillingAddress()->addData($billingAddress);
            $quote->getShippingAddress()->addData($shippingAddress);
     
            // Collect Rates and Set Shipping & Payment Method
           
            $shippingAddress = $quote->getShippingAddress();

            $shippingAddress->setCollectShippingRates(true) 
                            ->collectShippingRates()
                            ->setShippingMethod($oldOrder['shipping_method'])
                            ->setShippingDescription($oldOrder['shipping_description'])
                            ;

     
            // Set Sales Order Payment
            $order = $this->orderRepository->get($oldOrder['entity_id']);
            $paymentCode = $order->getPayment()->getMethod();
            //$quote->getPayment()->setMethod($paymentCode);            
            $quote->getPayment()->setMethod('cc');
            $quote->setInventoryProcessed(false);

            // Collect Totals & Save Quote
           $quote->collectTotals()
                 ->save();

            exit;

        
        } catch (\Exception $e) {
            //$logger->info('product is not available '.$e->getMessage());
            $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "custom_orders_report.log file"'];
            throw new \Exception($e->getMessage());
            return $result;                  
        }
        return $quote;

    }

    public function saveProductToCart($product, $params)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create('Magento\Checkout\Model\Cart');
        $cart->addProduct($product, $params);

    }
    public function saveQuoteItems($quote, $product, $price, $discountAmount,$discountPercent,$taxAmount, $taxPercent)
    {
        $priceInclTax = ($price+$taxAmount);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteItem = $objectManager->create('\Magento\Quote\Api\Data\CartItemInterface');
        //$quoteItem = $this->cartItemInterface;

        $quoteItem->setProduct($product);
        $quoteItem->setQty(1);
        $quoteItem->setBasePrice($price);
        $quoteItem->setPrice($price);
        $quoteItem->setCustomPrice($price);
        $quoteItem->setOriginalCustomPrice($price);
        $quoteItem->setBaseDiscountAmount($discountAmount);
        $quoteItem->setDiscountAmount($discountAmount);
        $quoteItem->setDiscountPercent($discountPercent);
        $quoteItem->setBaseTaxAmount($taxAmount);
        $quoteItem->setTaxAmount($taxAmount);
        $quoteItem->setTaxPercent($taxPercent);
        $quoteItem->setBaseRowTotal($price);
        $quoteItem->setRowTotal($price);
        $quoteItem->setRowTotalWithDiscount($price);
        $quoteItem->setBasePriceInclTax($priceInclTax);
        $quoteItem->setPriceInclTax($priceInclTax);
        $quoteItem->setBaseRowTotalInclTax($priceInclTax);
        $quoteItem->setRowTotalInclTax($priceInclTax);
        //$quoteItem->setProductType('simple');

        $quote->addItem($quoteItem);
    }

    public function createOrder($quote, $id)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
     
        $orderData = $this->quoteManagement->submit($quote);
        
        $optionsArr = [];
        foreach ($orderData->getAllItems() as $_item) {
            $itemId = $_item->getId();
            $productId = $_item->getProductId();
            
            // Get product_custom_options from catalog_session and save in sales_order_item table.
            $optionsArr = $this->catalogSession->getCustomOpData();
           
            $_item->setData('product_options', $optionsArr);
            $_item->save();

            // Save VC, consultation_type in customer_sessin
            $this->customerSession->setConsultationType('VC');
        }
    
        $orderData->setEmailSent(0);
        //$increment_id = $order->getRealOrderId();
        if ($orderData->getEntityId()) {
            
            $create_date = date('Y-m-d h:i:s');
            $newOrder = $this->generateOrderFactory->create()->load($id);
            $newOrder->setNewIncrementId($orderData->getRealOrderId())
                     ->setOrderDate($create_date)
                     ->save();

            $paymentDetails = $this->getPaymentInfo($orderData->getEntityId());
            
            echo "<pre>"; print_r($paymentDetails->getAdditionalInformation()); exit;
            $paymentAdditionInfoData = $paymentDetails->getAdditionalInformation();
            $profile_id = $paymentAdditionInfoData['profile_id'];
            $payment_id = $paymentAdditionInfoData['payment_id'];

            $result = [
                'error' => 0,
                'msg' => 'Order created successfully!',
                'quote_id' => $orderData->getQuoteId(),
                'order_id' => $orderData->getRealOrderId(),
                'grand_total' => $orderData->getGrandTotal(),
                'profile_id' => $profile_id,
                'payment_id' => $payment_id
            ];
    
    
        } else {

            $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "custom_orders_report.log file"'];
        }
    
        return $result;
    }

    // Return payment object
    public function getPaymentInfo($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();

        return $payment;
    }
    // Return array of response after customer charged.
    public function chargeCustomerProfile($profileid, $paymentprofileid, $amount)
    {
        echo "fsdf"; exit;
        $merchant_login_ID        = $this->getAutorizeLoginId(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $merchant_transaction_key = $this->getAutorizeTranskey(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */

        $merchantAuthentication = new MerchantAuthenticationType();

        //$merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
        //$merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
        $merchantAuthentication->setName($merchant_login_ID);
        $merchantAuthentication->setTransactionKey($merchant_transaction_key);
        
        // Set the transaction's refId
        $refId = 'ref' . time();

        $profileToCharge = new CustomerProfilePaymentType();
        $profileToCharge->setCustomerProfileId($profileid);
        $paymentProfile = new PaymentProfileType();
        $paymentProfile->setPaymentProfileId($paymentprofileid);
        $profileToCharge->setPaymentProfile($paymentProfile);

        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction"); 
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setProfile($profileToCharge);

        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new CreateTransactionController($request);
        

        echo $baseUrl = $this->storeManager->getStore()->getBaseUrl(); exit;

        if($baseUrl == 'https://enhance.md/'):
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        else:
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        endif;

        $authResponse = [];

        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == "Ok")
            {
                $tresponse = $response->getTransactionResponse();
                //echo "<pre>"; print_r($tresponse->getData()); exit;
                
                if ($tresponse != null && $tresponse->getMessages() != null)   
                {
                    $authResponse['error'] = 0;
                    $authResponse['trans_response_code'] = $tresponse->getResponseCode();
                    $authResponse['trans_auth_code'] = $tresponse->getAuthCode();
                    $authResponse['trans_id'] = $tresponse->getTransId();
                    $authResponse['trans_code'] = $tresponse->getMessages()[0]->getCode();
                    $authResponse['trans_message'] = $tresponse->getMessages()[0]->getDescription();
                }
                else
                {
                    if($tresponse->getErrors() != null)
                    {
                        $authResponse['error'] = 1;
                        $authResponse['trans_id'] = "";
                        $authResponse['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
                        $authResponse['trans_message'] = $tresponse->getErrors()[0]->getErrorText();            
                    }   
                }
            }
            else
            {
                $tresponse = $response->getTransactionResponse();
                if($tresponse != null && $tresponse->getErrors() != null)
                {
                    $authResponse['error'] = 1;
                    $authResponse['trans_id'] = "44";
                    $authResponse['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
                    $authResponse['trans_message'] = $tresponse->getErrors()[0]->getErrorText();                     
                }
                else
                {
                    $authResponse['error'] = 0;
                    $authResponse['trans_id'] = "33";
                    $authResponse['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
                    $authResponse['trans_message'] = $response->getMessages()->getMessage()[0]->getText();
                }
            }
        }
        else
        {
            $authResponse['error'] = 1;
            $authResponse['trans_id'] = "22";
            $authResponse['error_code'] = 0;
            $authResponse['trans_message'] = __("No response returned");
        }

        return $authResponse;
    }
    public function getAutorizeLoginId($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
       return $this->scopeInterface->getValue('payment/authnetcim/login', $scope);
    }

    public function getAutorizeTranskey($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
       return $this->scopeInterface->getValue('payment/authnetcim/trans_key', $scope);
    }
}