<?php

namespace CustomOrders\GenerateOrders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

require BP . '/vendor/authorizenet/authorizenet/autoload.php';//'vendor/autoload.php';
//require 'vendor/autoload.php';
//require_once 'constants/SampleCodeConstants.php';
use \net\authorize\api\contract\v1\MerchantAuthenticationType;
use \net\authorize\api\contract\v1\TransactionRequestType;
use \net\authorize\api\contract\v1\CustomerProfilePaymentType;
use \net\authorize\api\contract\v1\PaymentProfileType;
use \net\authorize\api\contract\v1\CreateTransactionRequest;

use \net\authorize\api\controller\CreateTransactionController;

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
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
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
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_transactionFactory          = $transactionFactory;
        $this->_invoiceRepository           = $invoiceRepository;
        parent::__construct($context);
    }

    public function createCustomOrder($orderDetailsArr)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
       
        // Store Info Setting
        $store = $this->storeRepository->get('default');
        $this->emulation->startEnvironmentEmulation($store->getId(), \Magento\Framework\App\Area::AREA_FRONTEND);
        $this->storeManager->getStore()->setWebsiteId($store->getWebsiteId());

        $orderData = $this->getOrderByIncrementId($orderDetailsArr['reference_increment_id']);
        $oldOrder = $orderData[0];
        
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
        $logger->info('Quote Created '.$quote->getId());

        // Create Order Function
        $res = $this->createOrder($quote, $orderDetailsArr, $oldOrder);
        //echo "<pre>"; print_r($res); exit;
        $logger->info('Order Created '.$res['order_id']);
        
        if($res['error'] == 0 && $res['profile_id'] != "" && $res['payment_id'] != "" )
        {
            $logger->info('charging customer for order '.$res['order_increment_id']);
            
            // Customer Charge via Authorizenet APi
            $authApiResponse = $this->chargeCustomerProfile(
                                        $res['profile_id'],
                                        $res['payment_id'],
                                        $res['grand_total'],
                                        $res['order_increment_id'],
                                        $res['order_id'],
                                        $orderDetailsArr['sku_option_id'],
                                        $orderDetailsArr['sku_option_type_id']
                            );

            $result[] = $res['order_id'];
        }
        
        return $result;
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
            $shippingAmount=0;
            $taxPercent=0;
            $itemsQty=0;
            $optionsArr = [];

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $options = [];
            $logger->info('Product sku - '.$sku);

            try {
                $product = $this->productRepository->get($sku);

            } catch (\Exception $e) {
                //$logger->info('product is not available '.$e->getMessage());
                $result = ['error' => 1, 'msg' => 'Order3 could not be created, Please check log "custom_orders_report.log file"'];
                throw new \Exception($e->getMessage());
            }


            // ============================= Check Subscriptions Type and price ====================================
            $subscriptionType = $this->isSubscription($optionId, $optionTypeId);

            $price         = $product->getPrice();
            $originalPrice = $product->getPrice();

            $subscriptionPice = $this->getSubscriptionPrice($product, $optionTypeId);
            
            if($subscriptionPice == 0)
            {
                $logger->info('Subscription Installment Price is not available! '.$optionTypeId);
                //return false;                
            }
            else
            {
                $price = $subscriptionPice;   // Set subscription price as original product price.
            }

            $product->setPrice($price);

            $subTotal   = $price;
            $grandTotal = (($price - $discountAmount) + $taxAmount + $shippingAmount);  // Calculaion grand_total.
            if($discountAmount >= $subTotal)
            {
                $grandTotal = 0;
            }
            if($grandTotal < 0)
            {
                $grandTotal = 0;
            }

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

            $this->saveQuoteItems($quote, $product, $price, $originalPrice, $discountAmount,$discountPercent,$taxAmount, $taxPercent);


            // ItemsQty & ItemsCount
            $itemsQty = 1;
            $itemsCount = 1;
            $quote->setItemsQty($itemsQty)
                ->setItemsCount($itemsCount);
            $quote->setSubtotal($subTotal)
                ->setBaseSubtotal($subTotal)
                ->setBaseGrandTotal($grandTotal)
                ->setGrandTotal($grandTotal);

            // ========================= Set Billing & Shiping Address =================================

            $quote->getBillingAddress()->addData($billingAddress);
            $quote->getShippingAddress()->addData($shippingAddress);

            // Collect Rates and Set Shipping & Payment Method
            $shippingAddress = $quote->getShippingAddress();

            //$logger->info('shipping_method Line 373 - '.$oldOrder['shipping_method']); flatrate_flatrate

            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                //->setShippingMethod($oldOrder['shipping_method'])
                ->setShippingMethod('flatrate_flatrate')
                ->setShippingDescription($oldOrder['shipping_description'])
            ;


            // Save data into quote_address table
            $quote->getShippingAddress()->setBaseDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setBaseTaxAmount($taxAmount);
            $quote->getShippingAddress()->setTaxAmount($taxAmount);
            $quote->getShippingAddress()->setBaseShippingAmount($shippingAmount);
            $quote->getShippingAddress()->setShippingAmount($shippingAmount);
            $quote->getShippingAddress()->setBaseSubtotal($subTotal);
            $quote->getShippingAddress()->setSubtotal($subTotal);
            $quote->getShippingAddress()->setBaseSubtotalWithDiscount($subTotal);
            $quote->getShippingAddress()->setSubtotalWithDiscount($subTotal);
            $quote->getShippingAddress()->setBaseSubtotalInclTax($subTotal);
            $quote->getShippingAddress()->setSubtotalInclTax($subTotal);
            $quote->getShippingAddress()->setBaseGrandTotal($grandTotal);
            $quote->getShippingAddress()->setGrandTotal($grandTotal);
            $quote->getShippingAddress()->setWeight($product->getWeight());

            // ============================ Set Sales Order Payment =======================================

            $order = $this->orderRepository->get($oldOrder['entity_id']);

            $logger->info('Payment Method '.$order->getPayment()->getMethod());
            $paymentCode = 'authorizenet';
            //$paymentCode = 'authnetcim';
            //$paymentCode = $order->getPayment()->getMethod();
            $quote->getPayment()->setMethod($paymentCode);
            $quote->setInventoryProcessed(false);


            // Collect Totals & Save Quote
            $quote->setTotalsCollectedFlag(true)
                ->collectTotals();
            $quote->save();


            // Store values for Frequency displayed on order item data in order detail page in admin.
            $option = $objectManager->get('Magento\Catalog\Model\Product\Option')->load($optionId);
            $optionTitle = $option->getTitle();
            $optionType = $option->getType();
            $value = $objectManager->get('Magento\Catalog\Model\Product\Option\Value')->load($option->getId());
            $valueTitle = $value->getTitle();
            $optionsArr = [];
            $optionsArr = [

                'info_buyRequest' =>
                    [
                        'qty' => 1,
                        'options' => [ $optionId => $optionTypeId]
                    ],
                'options' => [
                    [
                        "label" => "Frequency",
                        "value" => $subscriptionType,
                        "print_value"=> $subscriptionType,
                        "option_id" => $optionId,
                        "option_type"=> $optionType,
                        "option_value"=> $optionTypeId,
                        "custom_view"=>false
                    ]
                ]
            ];
            // Save custom option in catalog_session
            $catalogSession = $objectManager->get('Magento\Catalog\Model\Session');
            $catalogSession->setCustomOpData($optionsArr);

        } catch (\Exception $e) {
            //$logger->info('product is not available '.$e->getMessage());
            $result = ['error' => 1, 'msg' => 'Order1 could not be created, Please check log "custom_orders_report.log file"'];
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
    public function saveQuoteItems($quote, $product, $price, $originalPrice, $discountAmount,$discountPercent,$taxAmount, $taxPercent)
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
        $quoteItem->setOriginalPrice($originalPrice);
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

    // Return $option type is subscription/onetime
    public function isSubscription($optionId, $optionTypeId)
    {
        $optionObject = $this->option->load($optionId);
        $values = $this->value->getValuesCollection($optionObject);

        //$subscriptionPrice = $product->getPrice();
        $subscriptionType = 'Onetime';
        foreach ($values as $value) {
            $valueTitle = $value->getTitle();
            //$valuePrice = $value->getPrice();

            if($value->getOptionTypeId() == $optionTypeId)
            {
                $subscriptionType = $valueTitle;
                break;
            }
            else
            {
                continue;
            }
        }
        return $subscriptionType;
    }
    public function getSubscriptionPrice($product, $optionTypeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $subscriptionCollectionFactory = $objectManager->create('\ParadoxLabs\Subscriptions\Model\ResourceModel\Interval\Collection');

        $subscriptionCollectionFactory->addFieldToFilter('product_id', $product->getId());
        $subscriptionCollectionFactory->addFieldToFilter('value_id', $optionTypeId);

        $subscriptionPrice = 0;
        foreach ($subscriptionCollectionFactory as $option) {
            $subscriptionPrice = $option->getInstallmentPrice();
        }
        
        return $subscriptionPrice;

    }
    public function createOrder($quote, $customOrderData, $oldOrder)
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
        }

        $orderData->setEmailSent(0);

        //$increment_id = $order->getRealOrderId();
        if ($orderData->getEntityId()) {

            $create_date = date('Y-m-d H:i:s');
            $newOrder = $this->generateOrderFactory->create()->load($customOrderData['id']);
            $newOrder->setNewIncrementId($orderData->getRealOrderId())
                ->setNewOrderDate($create_date)
                ->save();

            $paymentDetails = $this->getPaymentInfo($oldOrder['entity_id']);
            
            $paymentAdditionInfoData = $paymentDetails->getAdditionalInformation();
            $profile_id = $paymentAdditionInfoData['profile_id'];
            $payment_id = $paymentAdditionInfoData['payment_id'];
           
            //$profile_id = 508610040;
            //$payment_id = 516124048;

            $logger->info('profile_id - '.$profile_id);
            $logger->info('payment_id - '.$payment_id);

            $result = [
                'error' => 0,
                'msg' => 'Order created successfully!',
                'quote_id' => $orderData->getQuoteId(),
                'order_increment_id' => $orderData->getRealOrderId(),
                'order_id' => $orderData->getEntityId(),
                'grand_total' => $orderData->getGrandTotal(),
                'profile_id' => $profile_id,
                'payment_id' => $payment_id
            ];


        } else {

            $result = ['error' => 1, 'msg' => 'Order2 could not be created, Please check log "custom_orders_report.log file"'];
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
    public function chargeCustomerProfile($profileId, $paymentProfileId, $amount, $orderIncrementId, $orderId,$optionId, $optionTypeId)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

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
        $profileToCharge->setCustomerProfileId($profileId);
        $paymentProfile = new PaymentProfileType();
        $paymentProfile->setPaymentProfileId($paymentProfileId);
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


        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        if($baseUrl == 'https://skinsolutions.md/'):
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        else:
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        endif;

        $authResponse = [];

        if ($response != null)
        {
            $logger->info('Whole Response : '.json_encode($response));

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

                    $logger->info('Customer Charged Successfully ');

                    if($authResponse['error'] == 0)
                    {
                         // Create Invoice                   
                        $invoice = $this->createInvoice(
                                    $optionId,
                                    $optionTypeId,
                                    $orderIncrementId,
                                    $profileId,
                                    $paymentProfileId,
                                    $tresponse->getTransId(),
                                    $tresponse->getAuthCode()
                            );

                        // Save other order payment data in payment table.
                        $this->saveOrderDetails($orderId, $orderIncrementId, $tresponse->getTransId(), $tresponse->getAuthCode(), $profileId, $paymentProfileId);

                    }
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

                $logger->info('Response '.json_encode($authResponse));
            }
            else
            {
                $tresponse = $response->getTransactionResponse();
               // echo "<pre>"; print_r($tresponse->getData()); exit;
                if($tresponse != null && $tresponse->getErrors() != null)
                {
                    $authResponse['error'] = 1;
                    $authResponse['trans_id'] = "44";
                    $authResponse['error_code'] = $tresponse->getErrors()[0]->getErrorCode();
                    $authResponse['trans_message'] = $tresponse->getErrors()[0]->getErrorText();
                }
                else
                {
                    $authResponse['error'] = 1;
                    $authResponse['trans_id'] = "33";
                    $authResponse['error_code'] = $response->getMessages()->getMessage()[0]->getCode();
                    $authResponse['trans_message'] = $response->getMessages()->getMessage()[0]->getText();
                }

                $logger->info('Response '.json_encode($authResponse));
            }
        }
        else
        {
            $logger->info('Response '.json_encode($authResponse));

            $authResponse['error'] = 1;
            $authResponse['trans_id'] = "22";
            $authResponse['error_code'] = 0;
            $authResponse['trans_message'] = __("No response returned");
        }

        return $authResponse;
    }
    public function createInvoice($optionId, $optionTypeId, $order_increment_id, $profileId, $paymentId, $transactionId, $transAuthCode)
    {
        $orderData = $this->getOrderByIncrementId($order_increment_id);            
        $orderArray = $orderData[0];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $invoiceService = $objectManager->create('\Magento\Sales\Model\Service\InvoiceService');
        $transaction = $objectManager->create('\Magento\Framework\DB\Transaction');
        $invoiceSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $order = $this->orderRepository->get($orderArray['entity_id']);

        try{
        
            if ($order->canInvoice()) {
                $invoice = $invoiceService->prepareInvoice($order);
                
                $invoice->register();
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment('Automatically INVOICED', false);
                $invoice->save();

                // order's status changes
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                      ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
                      ->setBaseTotalDue(0)
                      ->setTotalDue(0)
                      ->setBaseTotalPaid($order->getGrandTotal())
                      ->setTotalPaid($order->getGrandTotal())
                      ->save();

                $transactionSave = $transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
               // $invoiceSender->send($invoice);
                
                //Send Invoice mail to customer
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice creation #%1.', $invoice->getId())
                )
                    ->setIsCustomerNotified(true)
                    ->save();
            
            
                // Save subscription data. Call event dynamically.
                $eventManager = $objectManager->create('Magento\Framework\Event\ManagerInterface');

               
                // Generate Subscription Start
            
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
               

                // Check order is subscription order Or Onetime.
                $subscriptionType = $this->isSubscription($optionId, $optionTypeId);
                $logger->info('option id : '.$optionId );
                $logger->info('typeId option : '.$optionTypeId );

                if(($subscriptionType != "onetime")&&($subscriptionType != "Onetime")&&($subscriptionType != "One Time")&&($subscriptionType != "one time"))
                {

                    $logger->info('subscription creation start...'.$order->getEntityId());

                    $order = $this->orderRepository->get($order->getEntityId());
                    $quoteObj = $objectManager->create('\Magento\Quote\Model\Quote')->load($order->getQuoteId());

                    // Save subscription data. Call event dynamically.
                    /*$subscriptionObj = $this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quoteObj]);*/

                    /*$quoteObj->getPayment()->setAdditionalInformation('is_subscription_generated', 1)
                                        ->save();
                    $order->getPayment()->setAdditionalInformation('is_subscription_generated', 1)
                                        ->save();*/

                     $logger->info('Is subscription Generated...'.$order->getEntityId());

                    // Subscription Creation function 
                    $this->createCustomSubscription($order, $quoteObj, $profileId, $paymentId);
                   


                }
               
                // Generate Subscription End
               
                return $invoice;
            }
            else{
                return $msg = "cannot invoice generated";
            }

        }catch(\Exception $e)
        {
            echo $e->getMessage();
        }

        return false; 

    }

    public function saveOrderDetails($orderId, $order_increment_id, $transactionId, $transAuthCode, $profileId, $paymentId)
    {
        $order = $this->orderRepository->get($orderId);

        $tokenbaseId = $this->getTokenbaseDetails($order->getCustomerId());

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quote = $objectManager->create('\Magento\Quote\Model\Quote')->load($order->getQuoteId());
        // Set fields on sales_order_payment table.
        $order->getPayment()->setData('last_trans_id', $transactionId.'-capture')
                            ->setData('base_amount_authorized', $order->getGrandTotal())
                            ->setData('amount_authorized', $order->getGrandTotal())
                            ->setData('cc_approval', $transAuthCode)
                            ->setData('tokenbase_id', $tokenbaseId)
                            ->setData('method', 'authnetcim');
                            //->setData('method', 'CC');                                    

        $paymentAdditionCCInfoData = [];

        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();

        $paymentAdditionCCInfoData = array(
                'save'          => 1,
                "method_title"  => 'Credit Card (Authorize.Net CIM)',
                "response_code" => 1,
                "response_subcode"=> "",
                "response_reason_code"=> 1,
                "response_reason_text"=> "This transaction has been approved.",
                "approval_code"=> $transAuthCode,
                "auth_code"=> $transAuthCode,
                "avs_result_code"=> "P",
                "transaction_id"=> $transactionId,
                "reference_transaction_id"=> "",
                "invoice_number"=> $order_increment_id,
                "description"=> "",
                "amount"=> $order->getGrandTotal(),
                "method"=> "authnetcim",
                "transaction_type"=> "prior_auth_capture",
                "customer_id"=> $order->getCustomerId(),
                "card_code_response_code"=> "",
                "cavv_response_code"=> "",
                "acc_number"=> "XXXX".$order->getPayment()->getCcLast4(),
                "card_type"=> $order->getPayment()->getCcType(),
                "split_tender_id"=> "",
                "requested_amount"=> "",
                "balance_on_card"=> "",
                "profile_id"=>$profileId,
                "payment_id"=>$paymentId,
                "is_fraud"=> false,
                "is_error"=> false,
                "is_subscription_generated"=> 1
        );

        // set tokenbase_id
        $order->getPayment()->setTokenbaseId($tokenbaseId);
        $order->getPayment()->setAdditionalInformation($paymentAdditionCCInfoData)
                            ->save();

        $quoteObj = $objectManager->create('\Magento\Quote\Model\Quote')->load($order->getQuoteId());

        // Update quote table additional information
        $quoteObj->getPayment()->setTokenbaseId($tokenbaseId);
        $quoteObj->getPayment()->setAdditionalInformation($paymentAdditionCCInfoData)
                               ->save();

    }
    public function getTokenbaseDetails($customerId)
    {
        $cards = $this->card->getCollection();        
        $cards->addFieldToFilter('customer_id', $customerId);
        $tokenbaseHash = null;
        $tokenbaseId = null;
        foreach ($cards as $card) {
            $tokenbaseHash = $card->getHash();
        }
        if($tokenbaseHash != null)
        {
            $temp = $this->cardRepository->getById($tokenbaseHash);
            $card = $temp->getTypeInstance();
            $tokenbaseId = $card->getHash();    
        }
        return $tokenbaseId;
        
    }

    public function createCustomSubscription($order, $quote, $profileId, $paymentId)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subscriptionContext = $objectManager->get('\ParadoxLabs\Subscriptions\Observer\GenerateSubscriptionsObserver\Context');
        $generateSub  = $objectManager->get('ParadoxLabs\Subscriptions\Observer\GenerateSubscriptionsObserver');

        try{

            $quoteManager = $subscriptionContext->getItemManager();
            $config       =   $subscriptionContext->getConfig();

            $isModuleActive = $config->moduleIsActive();
            if ($isModuleActive == 0) {
                $logger->info('module disabled...'. $isModuleActive);
                return;
            }

            if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
                // Standard process
                $generateSub->processOrder($order);

                // Update date last user in paradoxlabs_stored_card table.
                $cardCollection = $this->card->getCollection();
                $cardCollection->addFieldToFilter('profile_id', $profileId);
                $cardCollection->addFieldToFilter('payment_id', $paymentId);


                foreach ($cardCollection as $cardValue) {
                    $cardValue->setUpdatedAt(date('Y-m-d H:i:s'))
                              ->setLastUse(date('Y-m-d H:i:s'))
                              ->save();
                }
            }

           
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        
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
