<?php
namespace Backend\SyncOrder\Controller\Adminhtml\SyncOrder;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order $order
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_order = $order;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $orderId = $this->_request->getParam('order_id');
        $order = $this->_order->load($orderId);

        try {   
                // ================== Code to send zeelify start ===================================




                // ================== Code to send zeelify end =====================================
            
                $this->messageManager->addSuccess(__("You synced Order to zeelify successfully"));
            
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Order Synched Failed. ".$e->getMessage()));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
