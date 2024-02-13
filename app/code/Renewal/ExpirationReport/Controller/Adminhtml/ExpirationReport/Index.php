<?php

namespace Renewal\ExpirationReport\Controller\Adminhtml\ExpirationReport;

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
        $resultPage->getConfig()->getTitle()->prepend(__('Prescription Expiration Report'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        //return true;
        return $this->_authorization->isAllowed('Renewal_OrderReport::renewal_orderreport');
    }

}

