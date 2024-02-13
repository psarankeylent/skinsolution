<?php
namespace LegacySubscription\Subscriptions\Controller\Adminhtml\Subscription;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;


class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    protected $customerSubscriptionFactory;
    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory
    )
    {
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;

        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {  
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/legacy_subscr_save.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        //$params = $this->getRequest()->getParams();
        //echo "<pre>"; print_r($params); exit;

        // If Tab is Legacy Subscription Orders then no need to save form and redirect.
        $activeTab = $this->getRequest()->getParam('active_tab');
        if($activeTab == 'Legacy')
        {
            return $resultRedirect->setPath('customer/index/index');
        }

        $status = $this->getRequest()->getPost('status');

        if ($status) {
           
            $model = $this->customerSubscriptionFactory->create();

            $id = $this->getRequest()->getPost('id');            
         
            try {
                   if ($id) {
                        $model = $this->customerSubscriptionFactory->create()->load($id);
                   }
                   else
                   {
                        $this->messageManager->addErrorMessage(__('This subscription no longer exists.'));
                        return $resultRedirect->setPath('*/*/');
                   }
                
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This subscription no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            
			
            try {
                
                $model->setData('status', $status)
                      ->setData('last_update_date', date('Y-m-d H:i:s'))
                      ->setData('id', $id)
                      ->save();

                $this->messageManager->addSuccessMessage(__('Profile status updated successfully.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('customer/subscription/subscriptionView', ['id' => $id, '_current' => true]);
                }
                return $resultRedirect->setPath('customer/index/edit', ['id' => $params['customer_id'], '_current' => true]);
            
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $logger->info("Something were wrong 1 ". $e->getMessage() );
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $logger->info("Something were wrong 2 ". $e->getMessage() );
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($this->getRequest()->getParams());
            return $resultRedirect->setPath('customer/subscription/subscriptionView', ['id' => $id, '_current' => true]);
        }
        return $resultRedirect->setPath('customer/subscription/subscriptionView', ['id' => $this->getRequest()->getParam('id'), '_current' => true]);
    }
}