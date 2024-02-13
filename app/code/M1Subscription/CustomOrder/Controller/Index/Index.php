<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace M1Subscription\CustomOrder\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,

        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,

        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,

        \M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollectionFactory $m1SubscriptionCollectionFactory,

        \Magento\Catalog\Model\Product $product,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order,

        PageFactory $resultPageFactory
        
    )
    {
        
        $this->storeManager         = $storeManager;

        $this->productRepository    = $productRepository;

        $this->quote                = $quote;
        $this->quoteManagement      = $quoteManagement;
        $this->orderSender          = $orderSender;
        $this->_orderRepository     = $orderRepository;
        $this->_invoiceService              = $invoiceService;
        $this->_invoiceCollectionFactory    = $invoiceCollectionFactory;
        $this->_transactionFactory          = $transactionFactory;
        $this->_invoiceRepository           = $invoiceRepository;

        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->order = $order;


        $this->m1SubscriptionCollectionFactory = $m1SubscriptionCollectionFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        


        $reference_id = 8626808;
        $collection = $this->m1SubscriptionCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter("reference_id", $reference_id);    

        if($collection->count()>0){
            foreach ($collection as $value) {
                $details    = $value->getData('details');
                $data       = unserialize($details);
            }
        }

        //echo "<pre>";
        //print_r($data); exit();

        $customerId     = $data['order_info']['customer_id'];
        $sku            = $data['order_item_info']['sku'];
        //$shipping       = $data['shipping_address'];
        //$billing        = $data['billing_address'];


        $shipping = [
                'firstname'     => $data['shipping_address']['firstname'],
                'lastname'      => $data['shipping_address']['lastname'],
                'street'        => $data['shipping_address']['street'],
                'city'          => $data['shipping_address']['city'],
                'region'        => $data['shipping_address']['region'],
                'region_id'     => $data['shipping_address']['region_id'],
                'postcode'      => $data['shipping_address']['postcode'],
                'country_id'    => $data['shipping_address']['country_id'],
                'telephone'     => $data['shipping_address']['telephone'],
                'save_in_address_book' => 0
            ];


        $billing = [
                'firstname'     => $data['billing_address']['firstname'],
                'lastname'      => $data['billing_address']['lastname'],
                'street'        => $data['billing_address']['street'],
                'city'          => $data['billing_address']['city'],
                'region'        => $data['billing_address']['region'],
                'region_id'     => $data['billing_address']['region_id'],
                'postcode'      => $data['billing_address']['postcode'],
                'country_id'    => $data['billing_address']['country_id'],
                'telephone'     => $data['billing_address']['telephone'],
                'save_in_address_book' => 0
            ];






        $quoteId = 1826478;
        $quote = $this->cartRepositoryInterface->get($quoteId); // load quote by quote id

        //$order = $this->quoteManagement->submit($quote);
        //echo $incrementId = $order->getIncrementId();

        //Set Billing and shipping Address to quote
        $quote->getBillingAddress()->addData($billing);
        $quote->getShippingAddress()->addData($shipping);

        // set shipping method
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('flatrate_flatrate'); //shipping method, please verify flat rate shipping must be enable flatrate_flatrate freeshipping_freeshipping
        $quote->setPaymentMethod('checkmo'); //payment method, please verify checkmo must be enable from admin
        $quote->setInventoryProcessed(false); //decrease item stock equal to qty
        $quote->save(); //quote save 
        // Set Sales Order Payment, We have taken check/money order 
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Quote Totals & Save
        $quote->collectTotals()->save();

        $order = $this->quoteManagement->submit($quote);
        echo $incrementId = $order->getIncrementId();



        exit;


        /*


        $reference_id = 8626808;
        $collection = $this->m1SubscriptionCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter("reference_id", $reference_id);    

        if($collection->count()>0){
            foreach ($collection as $value) {
                $details    = $value->getData('details');
                $data       = unserialize($details);
            }
        }

        //echo "<pre>";
        //print_r($data); exit();

        $customerId     = $data['order_info']['customer_id'];
        $sku            = $data['order_item_info']['sku'];
        //$shipping       = $data['shipping_address'];
        //$billing        = $data['billing_address'];


        $shipping = [
                'firstname'     => $data['shipping_address']['firstname'],
                'lastname'      => $data['shipping_address']['lastname'],
                'street'        => $data['shipping_address']['street'],
                'city'          => $data['shipping_address']['city'],
                'region'        => $data['shipping_address']['region'],
                'region_id'     => $data['shipping_address']['region_id'],
                'postcode'      => $data['shipping_address']['postcode'],
                'country_id'    => $data['shipping_address']['country_id'],
                'telephone'     => $data['shipping_address']['telephone'],
                'save_in_address_book' => 0
            ];


        $billing = [
                'firstname'     => $data['billing_address']['firstname'],
                'lastname'      => $data['billing_address']['lastname'],
                'street'        => $data['billing_address']['street'],
                'city'          => $data['billing_address']['city'],
                'region'        => $data['billing_address']['region'],
                'region_id'     => $data['billing_address']['region_id'],
                'postcode'      => $data['billing_address']['postcode'],
                'country_id'    => $data['billing_address']['country_id'],
                'telephone'     => $data['billing_address']['telephone'],
                'save_in_address_book' => 0
            ];




        $quoteId = 1826411;
        $quote = $this->cartRepositoryInterface->get($quoteId); // load quote by quote id
        $order = $this->quoteManagement->submit($quote);
        echo $incrementId = $order->getIncrementId();




        //$store      = $this->storeManager->getStore();
        //$storeId    = $store->getStoreId();
        //$websiteId  = $this->storeManager->getStore()->getWebsiteId();
        //$customer   = $this->customerFactory->create();
        //$customer->setWebsiteId($websiteId);


        //$customer->load($customerId);   // load customet by customer Id

        //$quote=$this->quote->create(); //Create object of quote

        /*
        $quote->setStore($store); //set store for our quote
        $customer= $this->customerRepository->getById($customerId);
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        $product=$this->productRepository->get($sku);
        $quote->addProduct($product,1);

        //Set Billing and shipping Address to quote
        $quote->getBillingAddress()->addData($billing);
        $quote->getShippingAddress()->addData($shipping);

        // set shipping method
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('flatrate_flatrate'); //shipping method, please verify flat rate shipping must be enable flatrate_flatrate freeshipping_freeshipping
        $quote->setPaymentMethod('checkmo'); //payment method, please verify checkmo must be enable from admin
        $quote->setInventoryProcessed(false); //decrease item stock equal to qty
        $quote->save(); //quote save 
        // Set Sales Order Payment, We have taken check/money order 
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Quote Totals & Save
        $quote->collectTotals()->save();
        */

        // Create Order From Quote
        //$quoteManagement = $objectManager->create('Magento\Quote\Model\QuoteManagement');

        /*
        $order = $this->quoteManagement->submit($quote);
        $order->setEmailSent(0);

        $payment = $order->getPayment();
        $payment->setMethod('authorizenet_acceptjs'); // Assuming 'test' is updated payment method
        $payment->save();
        $order->setState('new');
        $order->setStatus('archive');
        $order->setEmailSent(0);
        $order->save();
        $incrementId = $order->getIncrementId();
        $orderId = $order->getId();

        if($incrementId){
            $result['success']= $incrementId;
            $this->generateInvoice($orderId);
        }else{
            $result=['error'=>true,'msg'=>'Error occurs for Order placed'];
        }
        //return $result;

        */


        //$quote      = $this->cartRepositoryInterface->get($cartId);
        //$orderId    = $this->cartManagementInterface->placeOrder($quote->getId());
        //$order      = $this->order->load($orderId);

        //echo $incrementId = $order->getIncrementId();

        echo "<pre>";
        //print_r($quote);
        exit;









        echo "chk by skp"; exit;

        return $this->resultPageFactory->create();
    }

    public function generateInvoice($orderId){
        try 
        {
            $order = $this->_orderRepository->get($orderId);
            if ($order)
            {
                $invoices = $this->_invoiceCollectionFactory->create()
                  ->addAttributeToFilter('order_id', array('eq' => $order->getId()));

                $invoices->getSelect()->limit(1);

                if ((int)$invoices->count() !== 0) {
                  $invoices = $invoices->getFirstItem();
                  $invoice = $this->_invoiceRepository->get($invoices->getId());
                  return $invoice;
                }

                if(!$order->canInvoice()) {
                    return null;
                }

                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                $invoice->register();
                $invoice->getOrder()->setCustomerNoteNotify(false);
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment(__('Automatically INVOICED'), false);
                $transactionSave = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();

                return $invoice;
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }

    } // END generateInvoice


}


