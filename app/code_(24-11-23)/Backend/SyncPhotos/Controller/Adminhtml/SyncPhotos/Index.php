<?php
namespace Backend\SyncPhotos\Controller\Adminhtml\SyncPhotos;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->customerPhotosFactory = $customerPhotosFactory;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * Action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $customerId = $this->getRequest()->getParam('customer_id');
        if ($customerId) {

            try {
                $collection = $this->customerPhotosFactory->create()->getCollection();
                $collection->addFieldToFilter('customer_id', $customerId);
                $collection->addFieldToFilter('status',1);
                $customerPhotos = $collection->getData();
                //echo "<pre>"; print_r($customerPhotos); exit;


                // ================== Code to send zeelify start ===================================




                // ================== Code to send zeelify end =====================================
            
            
                // display success message
                $this->_messageManager->addSuccessMessage(__('You synced Customer Photos to zeelify successfully'));
                return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
                
            } catch (\Exception $e) {
                // display error message
                $this->_messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
            }
        }
        // display error message
        $this->_messageManager->addErrorMessage(__('We can\'t find a Customer Photos.'));
        return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
    }
}

