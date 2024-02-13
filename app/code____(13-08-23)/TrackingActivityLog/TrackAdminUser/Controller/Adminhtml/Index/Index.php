<?php

namespace TrackingActivityLog\TrackAdminUser\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $adminUserActionsLogsFactory;

    
    public function __construct(
        Action\Context $context,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        PageFactory $resultPageFactory
       
    ) {
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    { 
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    
	}
}

?>