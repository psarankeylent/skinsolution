<?php

namespace M1Subscription\Orders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $storeManager;
    protected $storeRepository;
    protected $productFactory;
    protected $productRepository;
    protected $customerFactory;
    protected $customerRepository;
    protected $orderService;
    protected $orderFactory;
    protected $emulation;
    protected $cartItemInterface;
    protected $catalogSession;
    protected $quoteFactory;
    protected $quoteManagement;
    protected $legacyFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Quote\Api\Data\CartItemInterface $cartItemInterface,
        \Magento\Catalog\Model\Session $catalogSession,
        \LegacySubscription\Subscriptions\Model\LegacyFactory $legacyFactory

    )
    {

        $this->quoteFactory = $quoteFactory;
        $this->quoteManagement = $quoteManagement;
        $this->orderService = $orderService;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->emulation = $emulation;
        $this->cartItemInterface = $cartItemInterface;
        $this->catalogSession = $catalogSession;
        $this->legacyFactory = $legacyFactory;

        parent::__construct($context);
    }

    public function createCustomOrder($order)
    {
        //echo "<pre>"; print_r($order); exit;
        $orders_created=[];
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];

        try{
         
            // Store Info Setting
            $store = $this->storeRepository->get('default');              
            $this->emulation->startEnvironmentEmulation($store->getId(), \Magento\Framework\App\Area::AREA_FRONTEND);
            $this->storeManager->getStore()->setWebsiteId($store->getWebsiteId());

            // Customer Creation Function
            $customer = $this->saveCustomer($store, $order);

            
            // Quote Creation Function
            $quote = $this->createQuote($store, $order, $customer);
            
            // Create Order Function
            return $result = $this->createOrder($quote, $order);
            //echo "<pre>"; print_r($result); exit;

        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }
        return $result;
    }

    // Return Customer Object
    public function saveCustomer($store, $order)
    {
        //echo "<pre>"; print_r($order);exit;

        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($store->getWebsiteId());
        $customer->loadByEmail($order['order_info']['customer_email']); // load customet by email address
        if (!$customer->getEntityId()) {
            //If not avilable then create this customer
           $customer->setWebsiteId($store->getWebsiteId())
                    ->setStore($store)
                    ->setFirstname($order['shipping_address']['firstname'])
                    ->setLastname($order['shipping_address']['lastname'])
                    ->setEmail($order['customer_email'])
                    ->save();
        }

        return $customer;

    }
    // Return \Magento\Framework\Model\Quote Object
    public function createQuote($store, $order, $customer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 

        try{
            $quote = $this->quoteFactory->create(); // Create Quote Object
            $quote->setStore($store); // Set Store
            $customer = $this->customerRepository->getById($customer->getEntityId());
            $quote->setCurrency();
            $quote->assignCustomer($customer); // Assign quote to Customer

            // Add items in the cart
            // Set data in quote_items table.
            $discountAmount=0;
            $taxAmount=0;
            $subTotal=0;
            $optionsArr = [];
                
            $options = [];
            $logger->info('Product Sku - '.$order['items']['sku']);

            // Get Product options associated with items if any
            $quoteId = $order['order_info']['entity_id'];

            if($quoteId != "")
            {
                $options = [];
                $quoteObj = $objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);
                $orderItems = $quoteObj->getAllVisibleItems();
                //echo "<pre>"; print_r($quote->getData()); exit;
         
                foreach ($orderItems as $item) {

                    //$options = $item->getProductOptions();
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $options = $options['info_buyRequest'];
                    //echo "<pre>"; print_r($options); exit;
                }

            }
            try {
                $product = $this->productRepository->get($order['items']['sku']);
            } catch (\Exception $e) {
                //$logger->info('product is not available '.$e->getMessage());
                $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];
                throw new \Exception($e->getMessage());                    
            }

            // ==================== Product Added To Cart(With Custom option if any) ================

            // Product Custom options If any
            $params = [];
            

            $qty = 1;
            $params['product'] = $product->getId();
            $params['qty'] = $qty;
            $params['options'] = $options;

            //$params['price'] = $subTotal;
            
            //echo "<pre>"; print_r($params); exit;

            // Create cart object and addtocart
            $cart = $objectManager->create('Magento\Checkout\Model\Cart');
            $cart->addProduct($product, $params);
            
            $price = $order['order_info']['subtotal'];
            $product->setPrice($price);
            //$product->setRegularPrice($order['order_info']['regular_price']);
            $subTotal = $price;
            //$grandTotal = (($subTotal - $discountAmount) + $taxAmount);
            
            //$price = $order['items']['price'];
            $priceInclTax = ($price+$taxAmount);
            //$quote->setData('remote_ip', $order['order_info']['remote_ip']);

            $quoteItem = $objectManager->create('\Magento\Quote\Api\Data\CartItemInterface');
            //$quoteItem = $this->cartItemInterface;

            $quoteItem->setProduct($product);
            $quoteItem->setQty($qty);
            $quoteItem->setBasePrice($price);
            $quoteItem->setPrice($price);
            $quoteItem->setCustomPrice($price);
            $quoteItem->setOriginalCustomPrice($price);
            $quoteItem->setBaseDiscountAmount($discountAmount);
            $quoteItem->setDiscountAmount($discountAmount);
            $quoteItem->setBaseTaxAmount($taxAmount);
            $quoteItem->setTaxAmount($taxAmount);
            $quoteItem->setBaseRowTotal($price);
            $quoteItem->setRowTotal($price);
            $quoteItem->setRowTotalWithDiscount($price);
            $quoteItem->setBasePriceInclTax($priceInclTax);
            $quoteItem->setPriceInclTax($priceInclTax);
            $quoteItem->setBaseRowTotalInclTax($priceInclTax);
            $quoteItem->setRowTotalInclTax($priceInclTax);
            //$quoteItem->setProductType('simple');

            $quote->addItem($quoteItem);

            // Set Billing & Shiping Address
            $quote->getBillingAddress()->addData($order['billing_address']);
            $quote->getShippingAddress()->addData($order['shipping_address']);
     
            // Collect Rates and Set Shipping & Payment Method
     
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)   // If false, invalid shipping method error will come
                            ->collectShippingRates()
                            ->setShippingMethod('freeshipping_freeshipping')
                            ->setShippingDescription('Free Shipment - Free');
     

             // Save data into quote_address table
            $quote->getShippingAddress()->setBaseDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setDiscountAmount($discountAmount);


            // Set Sales Order Payment
            //$quote->getPayment()->setMethod('authnetcim');
            $quote->getPayment()->setMethod($order['method_code']);
            $quote->setInventoryProcessed(false);

            // Collect Totals & Save Quote
           //$quote->setCouponCode('QASan')           // Dicount.
            $quote->collectTotals();
            $quote->save();

            /*$qPayment = $quote->getPayment();
            $qPayment->setTokenbaseId(222)->save();*/

        } catch (\Exception $e) {
            //$logger->info('product is not available '.$e->getMessage());
            $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];
            throw new \Exception($e->getMessage());
            return $result;                  
        }
        
        $logger->info("Quote Data ".json_encode($quote->getData()));

        return $quote;

    }
    public function createOrder($quote, $order)
    {
        //echo "<pre>"; print_r($quote->getData()); exit;
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('order for reference_id '.$order['profile_id']);
        try{         
            $orderData = $this->quoteManagement->submit($quote);
            //$orderData->setEmailSent(0);
        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }
        

        $discountAmount = $order['items']['discount_amount'];
        if (!empty($orderData)) {
            $logger->info("Order Data ".json_encode($orderData));

            $optionsArr = [];
            foreach ($orderData->getAllItems() as $_item) {
                $itemId = $_item->getId();
                
                // Get product_custom_options from catalog_session and save in sales_order_item table.
                //$optionsArr = $this->catalogSession->getCustomOpData();
                //echo "<pre>"; print_r($optionsArr); exit;
                // $_item->setData('product_options', $optionsArr);
                $_item->setData('base_discount_amount', $discountAmount);
                $_item->setData('discount_amount', $discountAmount);
                $_item->save();

                // Data stored in legacy subscription table
                $legacyModel = $this->legacyFactory->create();
                $legacyModel->setData('legacy_subscription', $order['profile_id'])
                            ->setData('order_increment_id', $orderData->getRealOrderId())
                            ->save();

            }
            
            // ================= Set Discount On sales_order ====================
            
            $ord = $this->orderFactory->create()->load($orderData->getId());
            $ord->setBaseDiscountAmount(-$discountAmount)
                ->setDiscountAmount(-$discountAmount)
                ->setBaseGrandTotal($ord->getGrandTotal() - $discountAmount)
                ->setGrandTotal($ord->getGrandTotal() - $discountAmount)
                ->save();

            // =============== Set Discount On quote_address table ================
            $quote->setGrandTotal($ord->getGrandTotal());
            $quote->setBaseGrandTotal($ord->getGrandTotal());
            /*$quote->getShippingAddress()->setBaseDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setBaseGrandTotal($quote->getGrandTotal() - $discountAmount);
            $quote->getShippingAddress()->setGrandTotal($quote->getGrandTotal() - $discountAmount);*/
            $quote->save();

            //$result = ['error' => 0, 'msg' => 'Order created successfully. Order ID '. $orderData->getRealOrderId()];
            $result[] = $orderData->getRealOrderId();
        } else {
            $logger->info("empty order Data ");

            $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];
        }

        return $result;
    }

    // Magento\Quote\Model\Quote @quote
    public function createNewOrderFromExistingQuote($quote)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscription_order_custom.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$logger->info('order for reference_id '.$quot);
        
        try{    

            $orderData = $this->quoteManagement->submit($quote);

            if(!empty($orderData))
            {
                //$orderData->setEmailSent(0);
                return $orderData->getRealOrderId();
            }
            
        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }
    }

}
