<?php
declare(strict_types=1);

namespace VirtualHub\UpdatePrescriptionOrder\Model;

class UpdatePrescriptionOrderItemManagement implements \VirtualHub\UpdatePrescriptionOrder\Api\UpdatePrescriptionOrderItemManagementInterface
{
    //protected $orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_orderFactory                = $orderFactory;
        $this->_invoiceCollectionFactory    = $invoiceCollectionFactory;
        $this->_invoiceService              = $invoiceService;
        $this->_transactionFactory          = $transactionFactory;
        $this->_invoiceRepository           = $invoiceRepository;
        $this->_orderRepository             = $orderRepository;
        $this->_productFactory              = $productFactory;
        $this->_orderCollectionFactory      = $orderCollectionFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function updatePrescriptionOrderItem($request)
    {
        
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];
        if(!is_array($request)){
            echo json_encode($response); die;
        }

        /*
        foreach ($request['items'] AS $requestItems){
            $orderIncrementId    = $requestItems['order_id'];
            $customerId     = $requestItems['customer_id'];
            $sku            = $requestItems['sku'];
            $status         = $requestItems['status'];
            $logger->info(' sku '.$sku);
        }
        */

        foreach ($request AS $requestValues){
            $incrementId     = $requestValues['order_increment_id'];
            $generateInvoice = $requestValues['generate_invoice'];
        }

        try 
        {
            $orderModel = $this->_orderFactory->create()->loadByIncrementId($incrementId);
            $orderId    = $orderModel->getId();

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

                $message = "Order status updated successfully";

                //return $invoice;
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }

        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;

    } // END updatePrescriptionOrderItem
}

