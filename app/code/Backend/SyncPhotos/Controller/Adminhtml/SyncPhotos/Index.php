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
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->customerPhotosFactory = $customerPhotosFactory;
        $this->_messageManager = $messageManager;
        $this->configHelper = $configHelper;
        $this->curl = $curl;
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
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sync_medical_photo.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
        
                $request['customer_id'] = $customerId;
                $bearerToken = $this->configHelper->getVirtualHubBearerToken();
                if ($bearerToken['success'] == True) {
                    $token = $bearerToken['token'];
                    $vhUrl = $this->configHelper->getUpdatePhoto();
                    $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer ' . $token];
                    $this->curl->setHeaders($headers);
                    $this->curl->post($vhUrl, json_encode($request));
                    $response = $this->curl->getBody();
                    $logger->info($response);
                }
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


