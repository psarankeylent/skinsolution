<?php

namespace Ssmd\Impersonation\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Backend\App\Action
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
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Impersonation');
        $resultPage->getConfig()->getTitle()->prepend(__('Impersonation'));

        return $resultPage;
        
        
	}
}

?>