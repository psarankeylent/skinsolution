<?php
declare(strict_types=1);

namespace VirtualHub\PaymentCapture\Cron;

class PaymentCaptureAndInvoice
{
    protected $logger;
    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_invoiceCollectionFactory    = $invoiceCollectionFactory;
        $this->_invoiceService              = $invoiceService;
        $this->_transactionFactory          = $transactionFactory;
        $this->_invoiceRepository           = $invoiceRepository;
        $this->_orderRepository             = $orderRepository;
        $this->_productFactory              = $productFactory;
        $this->_orderCollectionFactory      = $orderCollectionFactory;       
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob PaymentCaptureAndInvoice is executed.");
        $now        = new \DateTime();
        $toDate     = $now->format('Y-m-d H:i:s');
        $fromDate   = $now->modify("-15 days")->format('Y-m-d H:i:s');
	
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/nonrx-invoice.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $collection  = $this->_orderCollectionFactory->create()
                        ->addFieldToFilter('created_at', array('gteq' => $fromDate))
                        ->addFieldToFilter('created_at', array('lteq' => $toDate))
			->addFieldToFilter('entity_id', array('gteq' => '1361155'))
                        ->addFieldToFilter('status', 'pending');
        //echo "<pre>"; print_r($collection->getData());
        $prescription       = false;
        $canGenerateInvoice = true;
        $counter = 0;
        if(count($collection)>0){
            $logger->info("Count Invoice : " . count($collection));
            foreach ($collection as $orders) {
                $canGenerateInvoice = true;
                $prescription       = false;
                try {
                    $orderId = $orders->getId();
                    //$this->generateInvoice($orderId);
                    $logger->info($counter++ . "Order Increment ID : $orderId " . $orders->getIncrementId());
                    foreach ($orders->getAllItems() as $item) {
                        $productId =  $item->getProductId();
                        $product = $this->_productFactory->create()->load($productId);
                        if($product->getPrescription()){
                            $prescription = true;
                            $canGenerateInvoice = false;
                            continue;
                        }
                    } //END foreach for orders->getAllItems
                    if($canGenerateInvoice){
                        //echo "Invoice generating for NonRx orders";
                        $this->generateInvoice($orderId);
			$logger->info(" ============ Generated Invoice for :  " . $orderId);
                    }
                } catch (\Exception $e) {
                    $logger->addError("Virtualhub - Exception Error occurred: " . $e->getMessage() . "==> Order Increment ID : $orderId " . $orders->getIncrementId());
                // Handle the exception here (e.g., log the error, send email notification, etc.)
                // You can also add appropriate error handling logic to gracefully handle the exception.
                // If you want to continue the loop even after an exception, you can use `continue;` here.
                }
            } //END foreach for collection

        } // END if collection count 
        exit;
    } // END execute 

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
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
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


