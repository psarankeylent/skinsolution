<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Controller\Adminhtml\Order;
/**
 * CreateOrder class
 */
class CreateOrder extends \Magento\Backend\App\Action
{
    /**
     * resultPageFactory variable
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * jsonHelper variable
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * product variable
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * storeManager variable
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * addressFactory variable
     *
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $addressFactory;
    /**
     * resource variable
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    /**
     * orderFactory variable
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * paymentFactory variable
     *
     * @var \Magento\Sales\Model\Order\PaymentFactory
     */
    protected $paymentFactory;
    /**
     * orderItemFactory variable
     *
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItemFactory;
    /**
     * zeroDollarOrdersHistoryFactory variable
     *
     * @var \Ssmd\ZeroDollarOrders\Model\ZeroDollarOrdersHistoryFactory
     */
    protected $zeroDollarOrdersHistoryFactory;
    /**
     * taxHelper variable
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $taxHelper;
    /**
     * invoiceService variable
     *
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;
    /**
     * invoiceSender variable
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;
    /**
     * transaction variable
     *
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * Constructor function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Order\AddressFactory $addressFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\PaymentFactory $paymentFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param \Ssmd\ZeroDollarOrders\Model\ZeroDollarOrdersHistoryFactory $zeroDollarOrdersHistoryFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Catalog\Helper\Data $taxHelper
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\PaymentFactory $paymentFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Ssmd\ZeroDollarOrders\Model\ZeroDollarOrdersHistoryFactory $zeroDollarOrdersHistoryFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Catalog\Helper\Data $taxHelper,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->addressFactory = $addressFactory;
        $this->resource = $resource->getConnection();
        $this->orderFactory = $orderFactory;
        $this->paymentFactory = $paymentFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->zeroDollarOrdersHistoryFactory = $zeroDollarOrdersHistoryFactory;
        $this->authSession = $authSession;
        $this->taxHelper = $taxHelper;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transaction = $transaction;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // get post params
        $postData = $this->getRequest()->getPostValue();
        // Load store
        $store = $this->storeManager->getStore();
        // Get Website Id
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        try {

           // load customer by id
           $customer= $this->customerRepository->getById($postData['customer_id']);
            $order = $this->orderFactory->create()
            ->setIncrementId($this->getSequenceNumber()) 
            ->setStoreId($store->getId()) 
            ->setQuoteId(0)
            ->setGlobalCurrencyCode($store->getCurrentCurrencyCode())
            ->setBaseCurrencyCode($store->getCurrentCurrencyCode())
            ->setStoreCurrencyCode($store->getCurrentCurrencyCode())
            ->setOrderCurrencyCode($store->getCurrentCurrencyCode());

            // set customer
            $order->setCustomerEmail($customer->getEmail())
            ->setCustomerFirstname($customer->getFirstname())
            ->setCustomerLastname($customer->getLastname())
            ->setCustomerGroupId($customer->getGroupId())
            ->setCustomerIsGuest(0)
            ->setCustomer($customer);
            // Load shipping address by id
            $shpipingAddress = $this->getAddressById($postData['shipping_address'], $store->getId());
            // Load billing address by id
            $billingAddress = $this->getAddressById($postData['billing_address'], $store->getId());
            // set shipping and billing address
            $order->setBillingAddress($billingAddress)
              ->setShippingAddress($shpipingAddress);

             // create payment method
              $payment = $this->paymentFactory->create();
              $payment->setMethod('free');

              $order->setPayment($payment);
              $order->setShippingMethod('freeshipping_freeshipping');
              
              $productIds = explode(',',$postData['items']);
              $info_buyRequest = array("info_buyRequest" => array("qty" => 1 ));
              $subtotal = 0;
              foreach ($productIds as $productId) {
                   // Load product By Id
                   $product = $this->product->load($productId);
                   $excTaxPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
                   $incTaxprice = $this->taxHelper->getTaxPrice($product, $product->getFinalPrice(), true);
                   $taxAmount = $incTaxprice - $excTaxPrice;
                   $orderItem = $this->orderItemFactory->create()
                   ->setStoreId($store->getId())
                    ->setQuoteItemId(0)
                    ->setQuoteParentItemId(null)
                    ->setProductId($product->getId())
                    ->setProductType($product->getTypeId())
                    ->setProductOptions($info_buyRequest)
                    ->setQtyBackordered(null)
                    ->setTotalQtyOrdered(1)
                    ->setQtyOrdered(1)
                    ->setName($product->getName())
                    ->setSku($product->getSku())
                    ->setPrice($product->getPrice())
                    ->setBasePrice($product->getPrice())
                    ->setOriginalPrice($product->getPrice())
                    ->setBaseOriginalPrice($product->getPrice())
                    ->setTaxAmount($taxAmount)
                    ->setBaseTaxAmount($taxAmount)
                    ->setRowTotal($product->getPrice())
                    ->setBaseRowTotal($product->getPrice())
                    ->setBaseDiscountAmount($incTaxprice)
                    ->setDiscountAmount($incTaxprice)
                    ->setPriceInclTax($incTaxprice)
                    ->setBasePriceInclTax($incTaxprice)
                    ->setRowTotalInclTax($incTaxprice)
                    ->setBaseRowTotalInclTax($incTaxprice);
                    $subtotal += floatval($incTaxprice);
                    // add product to sales order
                    $order->addItem($orderItem);
              }
                // set order total
                $order->setBaseSubtotal($subtotal)
                    ->setBaseShippingAmount(0)
                    ->setShippingAmount(0)
                    ->setBaseShippingInclTax(0)
                    ->setShippingInclTax(0)
                    ->setBaseTaxAmount(0)
                    ->setSubtotal($subtotal)
                    ->setTaxAmount(0)
                    ->setBaseGrandTotal(0)
                    ->setGrandTotal(0)
                    ->setBaseSubtotalInclTax($subtotal)
                    ->setBaseDiscountAmount(-$subtotal)
                    ->setDiscountAmount(-$subtotal)
                    ->setBaseTotalDue(0)
                    ->setSubtotalInclTax($subtotal)
                    ->setTotalDue(0);

                // Save Order
                $order->setState('processing');
                $order->setStatus('processing');
                $order->setShippingDescription('Free');
                $order->setCanSendNewEmailFlag(true);
                $order->save();
              // create invoice
              $this->createInvoice($order);
              // save order history
              $this->saveOrderHistory($order);
              // add success mesaage
              $this->messageManager->addSuccessMessage(__('Zero Dollar Orders Created Successfully!.'));
              // Return Json response
            return $this->jsonResponse(["status" => true,'msg' => 'Zero Dollar Orders Created Successfully','order_id' => $order->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }
    /**
     * Get Address By id
     *
     * @param int $id
     * @param int $storeId
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getAddressById($id, $storeId = 1){
        $address = $this->addressFactory->create()->load($id);
        return $this->addressFactory->create()
        ->setStoreId($storeId)
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setStreet($address->getStreet())
            ->setCity($address->getCity())  
            ->setCountryId($address->getCountryId())   
            ->setRegion($address->getRegion())       
            ->setPostcode($address->getPostcode())  
            ->setTelephone($address->getTelephone());

    }
    /**
     * Get Sequence Number
     *
     * @return string
     */
    private function getSequenceNumber() {
        $this->resource->query('CALL GetNextZeroDollarIncrement(@nextSeqNumber)');
        return  $this->resource->query('SELECT @nextSeqNumber')->fetchColumn();
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * Save Order History
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function saveOrderHistory($order) {
        $user = $this->authSession->getUser();
        $this->zeroDollarOrdersHistoryFactory->create()
        ->setOrderId($order->getId())
        ->setIncrementId($order->getIncrementId())
        ->setCustomerId($order->getCustomerId())
        ->setAdminId($user->getUserId())
        ->save();
    }
    /**
     * Create Invoice
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    private function createInvoice($order)
    {
        if($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
            ->setIsCustomerNotified(true)
            ->save();
        }
    }
}
