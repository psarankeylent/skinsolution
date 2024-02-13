<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\SubscriptionLable\Rewrite\Magento\Sales\Controller\Adminhtml\Order;

class View extends \Magento\Sales\Controller\Adminhtml\Order\View
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_view';

    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Orders'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            }
            // code for display subscription order or not //
            $objectManager          = \Magento\Framework\App\ObjectManager::getInstance();
            $subscription           = $objectManager->create("ParadoxLabs\Subscriptions\Model\Log");
            $subscriptionCollection = $subscription->getCollection();
                                    $subscriptionCollection->addFieldToFilter("order_increment_id", $order->getIncrementId());

            if($subscriptionCollection->Count()>0){
                $incrementId = $order->getIncrementId().' (Subscription)';
            }else{
                $incrementId = $order->getIncrementId();
            }

            $resultPage->getConfig()->getTitle()->prepend(sprintf("#%s", $incrementId));
            return $resultPage;
        }
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }

}

