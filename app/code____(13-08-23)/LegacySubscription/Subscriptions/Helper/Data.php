<?php

namespace LegacySubscription\Subscriptions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
 
class Data extends AbstractHelper
{
    protected $storeManager;
    protected $storeRepository;
    protected $productFactory;
    protected $productRepository;
    protected $formkey;
    protected $quote;
    protected $quoteManagement;
    protected $customerFactory;
    protected $customerRepository;
    protected $orderService;
    protected $orderFactory;
    protected $emulation;
    protected $cartItemInterface;
    protected $catalogSession;
    protected $legacyFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Quote\Api\Data\CartItemInterface $cartItemInterface,
        \Magento\Catalog\Model\Session $catalogSession,
        \LegacySubscription\Subscriptions\Model\LegacyFactory $legacyFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->orderFactory = $orderFactory;
        $this->emulation = $emulation;
        $this->cartItemInterface = $cartItemInterface;
        $this->catalogSession = $catalogSession;
        $this->legacyFactory = $legacyFactory;
        parent::__construct($context);
    }
 
    public function createCustomOrder($order)
    {
        //echo "<pre>"; print_r($order); exit;

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
            $customerObj = $this->customerRepository->getById($order['customer_id']);

            $email = $customerObj->getEmail();
            $customer = $this->saveCustomer($store, $email, $order['shipping_address']);

            
            // Quote Creation Function
            $quote = $this->createQuote($store, $order, $customer);
            
            // Create Order Function
            $result = $this->createOrder($quote, $order);
            echo "<pre>"; print_r($result); exit;

        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }
        return $result;
    }

    // Return Customer Object
    public function saveCustomer($store, $email, $shippingAddress)
    {
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
    // Return \Magento\Framework\Model\Quote Object
    public function createQuote($store, $order, $customer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 

        try{
            $quote = $this->quote->create(); // Create Quote Object
            $quote->setStore($store); // Set Store
            $customer = $this->customerRepository->getById($customer->getEntityId());
            $quote->setCurrency();
            $quote->assignCustomer($customer); // Assign quote to Customer

            $cardRepository = $objectManager->create('\ParadoxLabs\TokenBase\Api\CardRepositoryInterface');

            $tokenbaseHash = null;
            $tokenbaseId = null;

            $card = $objectManager->create('\ParadoxLabs\TokenBase\Model\Card')->load(1214, 'customer_id');
            $tokenbaseHash = $card->getHash();

            /*$cards->addFieldToFilter('customer_id', $customer->getEntityId());*/
           // echo "fff"; exit;

           
            if($tokenbaseHash != null)
            {
                $temp = $cardRepository->getById($tokenbaseHash);
                $card = $temp->getTypeInstance();
                $tokenbaseId = $card->getHash();    
            }



     
            // Add items in the cart
            // Set data in quote_items table.
            $discountAmount=0;
            $taxAmount=0;
            $subTotal=0;
            $itemsQty=0;
            $optionsArr = [];
                
            $options = [];
            $logger->info('Product Sku - '.$order['items']['sku']);

            try {
                $product = $this->productRepository->get($order['items']['sku']);
            } catch (\Exception $e) {
                //$logger->info('product is not available '.$e->getMessage());
                $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];
                throw new \Exception($e->getMessage());                    
            }
            
            $price = $order['items']['subtotal'];
            $product->setPrice($price);
            $product->setRegularPrice($order['items']['regular_price']);
            $subTotal = $price;

            // ==================== Product Added To Cart(With Custom option if any) ================

            // Product Custom options If any
            $params = [];
            if((array_key_exists('option_id', $order['items'])) && (array_key_exists('option_type_id', $order['items'])))
            {
                $options[$order['items']['option_id']] = $order['items']['option_type_id'];
                $params['options'] = $options;

            }

            $qty = 1;
            $params['product'] = $product->getId();
            $params['qty'] = $qty;
            //$params['price'] = $subTotal;
            
            //echo "<pre>"; print_r($params); exit;

            // Create cart object and addtocart
            $cart = $objectManager->create('Magento\Checkout\Model\Cart');
            $cart->addProduct($product, $params);
            
            $priceInclTax = ($price+$taxAmount);

            // ============== Discount =================
            $discountAmount = $order['items']['discount_amount'];

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

            // ItemsQty
            $itemsQty = $itemsQty + $qty;

            $optionId = $order['items']['option_id'];
            $optionTypeId = $order['items']['option_type_id'];

            $option = $objectManager->get('Magento\Catalog\Model\Product\Option')->load($optionId);

            $optionTitle = $option->getTitle();
            $optionType = $option->getType();
            
            $value = $objectManager->get('Magento\Catalog\Model\Product\Option\Value')->load($option->getId());
            $valueTitle = $value->getTitle();

            $subscriptionType = 'Onetime';
            //$productId = $item['product_id'];
            
            $optionsArr = [

                'info_buyRequest' => 
                    [
                        'qty' => $qty,
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

            //$dataArr = $optionsArr;
            //echo "<pre>"; print_r($optionsArr); exit;

            // Save custom options in catalog_session
            $this->catalogSession->setCustomOpData($optionsArr);   

            
            
            // Products count
            $itemsCount = 1;
            // Grand total
            $grandTotal = (($subTotal-$discountAmount));
            
            $quote->setItemsQty($itemsQty)
                  ->setItemsCount($itemsCount);
                 
            $quote->setSubtotal($subTotal)
                  ->setBaseSubtotal($subTotal)
                  ->setBaseGrandTotal($grandTotal)
                  ->setGrandTotal($grandTotal);

           // echo "<pre>"; print_r($order['billing_address']); exit;
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
            $quote->getPayment()->setMethod('CC');
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
        return $quote;

    }
    public function createOrder($quote, $order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

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

            $optionsArr = [];
            foreach ($orderData->getAllItems() as $_item) {
                $itemId = $_item->getId();
                
                // Get product_custom_options from catalog_session and save in sales_order_item table.
                $optionsArr = $this->catalogSession->getCustomOpData();
                //echo "<pre>"; print_r($optionsArr); exit;
                $_item->setData('product_options', $optionsArr);
                $_item->setData('base_discount_amount', $discountAmount);
                $_item->setData('discount_amount', $discountAmount);
                $_item->save();

                // Data stored in legacy subscription table
                $legacyModel = $this->legacyFactory->create()->load($order['legacy_id']);
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
            $quote->getShippingAddress()->setBaseDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setDiscountAmount($discountAmount);
            $quote->getShippingAddress()->setBaseGrandTotal($quote->getGrandTotal() - $discountAmount);
            $quote->getShippingAddress()->setGrandTotal($quote->getGrandTotal() - $discountAmount);
            $quote->save();

            $result = ['error' => 0, 'msg' => 'Order created successfully. Order ID '. $orderData->getRealOrderId()];
        } else {
            $result = ['error' => 1, 'msg' => 'Order could not be created, Please check log "ssmd_create_order_report.log file"'];
        }

        return $result;
    }

}