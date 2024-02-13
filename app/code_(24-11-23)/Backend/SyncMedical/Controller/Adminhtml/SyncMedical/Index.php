<?php
namespace Backend\SyncMedical\Controller\Adminhtml\SyncMedical;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->customerFactory = $customerFactory;
        $this->medicalFactory = $medicalFactory;
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
                
                $collection = $this->medicalFactory->create()->getCollection();
                $collection->addFieldToFilter('customer_id', $customerId);
                $medicalHistory = $collection->getData();
               // print_r($medicalHistory); exit;


                // ================== Code to send zeelify start ===================================




                // ================== Code to send zeelify end =====================================
            
                // display success message
                $this->_messageManager->addSuccessMessage(__('You synced Medical History to zeelify successfully'));
                return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
                
            } catch (\Exception $e) {
                // display error message
                $this->_messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
            }
        }
        // display error message
        $this->_messageManager->addErrorMessage(__('We can\'t find a Medical History.'));
        return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
    }
}

