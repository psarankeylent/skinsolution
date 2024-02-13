<?php

namespace Renewal\CcReports\Controller\Adminhtml\Cc;

use Magento\Framework\Exception\LocalizedException;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create(); 
        $resultPage->getConfig()->getTitle()->prepend(__('Credit Cart Expiration Report')); 
        return $resultPage;
    }

}

