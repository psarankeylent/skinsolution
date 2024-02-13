<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Controller\Adminhtml\Order;

/**
 * Review class
 */
class Review extends \Magento\Backend\App\Action
{
    /**
     * resultPageFactory variable
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * customerRepository variable
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * coreRegistry variable
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Constructor function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerRepository = $customerRepository;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer') ?? null;

        $customer = $this->customerRepository->getById($customerId);
        if(!$customer->getId()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        $this->coreRegistry->register('ssmd_zerodollarorders_customer', $customer);
        $resultPage = $this->resultPageFactory->create();
        return $resultPage->setActiveMenu('Magento_Sales::sales');
    }
}
