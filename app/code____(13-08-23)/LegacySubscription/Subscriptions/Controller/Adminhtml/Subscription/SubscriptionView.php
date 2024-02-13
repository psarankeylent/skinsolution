<?php

namespace LegacySubscription\Subscriptions\Controller\Adminhtml\Subscription;

/**
 * Subscriptions form
 */
class SubscriptionView extends \Magento\Backend\App\Action
{

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \LegacySubscription\Subscriptions\Model\CustomerSubscription $customerSubscription
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \LegacySubscription\Subscriptions\Model\CustomerSubscription $customerSubscription,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->customerSubscription = $customerSubscription;
        $this->customerRepository = $customerRepository;
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context);
    }

    /**
     * Subscriptions list action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $initialized = $this->initModels();
       
        if ($initialized !== true) {
            $this->messageManager->addErrorMessage(__('Could not load the requested subscription.'));

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/edit/index/id/'.$this->getRequest()->getParam('id') );
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        
        /**
         * Set active menu item
         */
        /*$resultPage->setActiveMenu('ParadoxLabs_Subscriptions::subscriptions_manage');
        $resultPage->getConfig()->getTitle()->prepend(
            __(
                'Subscription # %1',
                $this->registry->registry('current_subscription')->getIncrementId()
            )
        );*/

        /**
         * Add breadcrumb item
         */
        //$resultPage->addBreadcrumb(__('Subscriptions'), __('Subscriptions'));
        //$resultPage->addBreadcrumb(__('Manage Subscriptions'), __('Manage Subscriptions'));

        return $resultPage;
    }

    /**
     * Initialize subscription/customer models for the current request.
     *
     * @return bool Successful or not
     */
    protected function initModels()
    {
        /**
         * Load subscription by ID.
         */
        $id = (int)$this->getRequest()->getParam('id');

        try {
            /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
            $subscription = $this->customerSubscription->load($id);
        } catch (\Exception $e) {
            return false;
        }

        /**
         * If it doesn't exist, fail (redirect to grid).
         */
        if ($id < 1 || $subscription->getId() != $id) {
            return false;
        }

        //$this->registry->register('current_subscription', $subscription);

        /**
         * Load and set customer (if any) for TokenBase.
         */
        if ($subscription->getCustomerId() > 0) {
            try {
                $customer = $this->customerRepository->getById($subscription->getCustomerId());

                if ($customer->getId() == $subscription->getCustomerId()) {
                    $this->registry->register('current_customer', $customer);
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // Throw a warning, but proceed even if customer doesn't exist.
                $this->messageManager->addErrorMessage(__('Could not load the subscription customer (ID %1).', $subscription->getCustomerId()));
            }
        }

        return true;
    }
}
