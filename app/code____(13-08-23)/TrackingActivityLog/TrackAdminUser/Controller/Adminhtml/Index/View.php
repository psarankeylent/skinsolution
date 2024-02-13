<?php

namespace TrackingActivityLog\TrackAdminUser\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Backend\App\Action
{
    protected $helperData;

    
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
       
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Activity Logs'));

        return $resultPage;
        
        
	}
}

?>