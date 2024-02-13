<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Controller\Adminhtml\Order;
/**
 * Index class
 */
class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage->setActiveMenu('Magento_Sales::sales');
    }
}

