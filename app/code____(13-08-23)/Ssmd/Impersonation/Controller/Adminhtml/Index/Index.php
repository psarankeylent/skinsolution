<?php

namespace Ssmd\Impersonation\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $helperData;

    
    public function __construct(
        Action\Context $context,
        \Ssmd\Impersonation\Helper\Data $helperData,
        PageFactory $resultPageFactory
       
    ) {
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    { 
        if($this->helperData->isModuleEnabled())
        {
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
        }
        else
        {
            $this->_redirect("admin/dashboard/index");
        }
        
	}
}

?>