<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ssmd\MultipleCoupons\Controller\Adminhtml\Order\Create;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Sales\Controller\Adminhtml\Order\Create;

class LoadBlock extends \Magento\Sales\Controller\Adminhtml\Order\Create\LoadBlock
{
    protected const ACTION_SAVE = 'save';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * Process request data with additional logic for saving quote and creating order
     *
     * @param string $action
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _processActionData($action = null)
    {
        $eventData = [
            'order_create_model' => $this->_getOrderCreateModel(),
            'request_model' => $this->getRequest(),
            'session' => $this->_getSession(),
        ];

        $this->_eventManager->dispatch('adminhtml_sales_order_create_process_data_before', $eventData);

        $data = $this->getRequest()->getPost('order');
        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }

        $removeCouponCode = '';
        if (isset($data) && isset($data['remove-coupon']['code'])) {
            $removeCouponCode = trim($data['remove-coupon']['code']);
        }

        $oldCouponCode = $this->_getQuote()->getCouponCode();
        $oldCouponCodes = [];
        if ($oldCouponCode)
            $oldCouponCodes = explode(',', $oldCouponCode);
        $couponCodeExistsFlag = false;
        $removeCouponCodeDoesntExistFlag = false;

        if ($couponCode) {
            if (in_array($couponCode, $oldCouponCodes)) {
                $couponCodeExistsFlag = true;
            }
        }

        if ($removeCouponCode) {
            if (!in_array($removeCouponCode, $oldCouponCodes)) {
                $removeCouponCodeDoesntExistFlag = true;
            }
        }
        /**
         * Import post data, in order to make order quote valid
         */
        if ($data) {
            $this->_getOrderCreateModel()->importPostData($data);
        }

        /**
         * Initialize catalog rule data
         */
        $this->_getOrderCreateModel()->initRuleData();

        /**
         * init first billing address, need for virtual products
         */
        $this->_getOrderCreateModel()->getBillingAddress();

        /**
         * Flag for using billing address for shipping
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual()) {
            $syncFlag = $this->getRequest()->getPost('shipping_as_billing');
            $shippingMethod = $this->_getOrderCreateModel()->getShippingAddress()->getShippingMethod();
            if ($syncFlag === null
                && $this->_getOrderCreateModel()->getShippingAddress()->getSameAsBilling() && empty($shippingMethod)
            ) {
                $this->_getOrderCreateModel()->setShippingAsBilling(1);
            } elseif ($syncFlag !== null) {
                $this->_getOrderCreateModel()->setShippingAsBilling((int)$syncFlag);
            }
        }

        /**
         * Change shipping address flag
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')
        ) {
            $this->_getOrderCreateModel()->resetShippingMethod(true);
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual() && $this->getRequest()->getPost(
                'collect_shipping_rates'
            )
        ) {
            $this->_getOrderCreateModel()->collectShippingRates();
        }

        /**
         * Apply mass changes from sidebar
         */
        if (($data = $this->getRequest()->getPost('sidebar')) && $action !== self::ACTION_SAVE) {
            $this->_getOrderCreateModel()->applySidebarData($data);
        }

        $this->_eventManager->dispatch('adminhtml_sales_order_create_process_item_before', $eventData);

        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->_getOrderCreateModel()->addProduct($productId, $this->getRequest()->getPostValue());
        }

        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items')
            && $action !== self::ACTION_SAVE
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->_getOrderCreateModel()->addProducts($items);
        }

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $this->_getOrderCreateModel()->updateQuoteItems($items);
        }

        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->_getOrderCreateModel()->removeItem($removeItemId, $removeFrom);
            $this->_getOrderCreateModel()->recollectCart();
        }

        /**
         * Move quote item
         */
        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        $moveQty = (int)$this->getRequest()->getPost('qty');
        if ($moveItemId && $moveTo) {
            $this->_getOrderCreateModel()->moveQuoteItem($moveItemId, $moveTo, $moveQty);
        }

        $this->_eventManager->dispatch('adminhtml_sales_order_create_process_item_after', $eventData);

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
        }

        $eventData = [
            'order_create_model' => $this->_getOrderCreateModel(),
            'request' => $this->getRequest()->getPostValue(),
        ];

        $this->_eventManager->dispatch('adminhtml_sales_order_create_process_data', $eventData);

        $this->_getOrderCreateModel()->saveQuote();

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Saving of giftmessages
         */
        $giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }

        /**
         * Importing gift message allow items from specific product grid
         */
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonDecode($data)
            );
        }

        /**
         * Importing gift message allow items on update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }

        $newCouponCode = $this->_getQuote()->getCouponCode();
        $newCouponCodes = [];

        if ($newCouponCode)
            $newCouponCodes = explode(',', $newCouponCode);

        if (!empty($removeCouponCode)) {
            if ($removeCouponCodeDoesntExistFlag) {
                $this->messageManager->addErrorMessage(
                    __(
                        'The "%1" coupon code couldn\'t be deleted. Verify the code and try again.',
                        $this->escaper->escapeHtml($removeCouponCode)
                    )
                );
            } else {
                $this->messageManager->addSuccessMessage(__("The coupon code $removeCouponCode is removed."));
            }
        }

        if (!empty($couponCode)) {
            $isApplyDiscount = false;
            foreach ($this->_getQuote()->getAllItems() as $item) {
                if (!$item->getNoDiscount()) {
                    $isApplyDiscount = true;
                    break;
                }
            }
            if (!$isApplyDiscount) {
                $this->messageManager->addErrorMessage(
                    __(
                        '"%1" coupon code was not applied. Do not apply discount is selected for item(s)',
                        $this->escaper->escapeHtml($couponCode)
                    )
                );
            } else {
                if ($couponCodeExistsFlag) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'The "%1" coupon code is already applied.',
                            $this->escaper->escapeHtml($couponCode)
                        )
                    );
                } else {
                    if (in_array($couponCode, $newCouponCodes)) {
                        $this->messageManager->addSuccessMessage(__("The coupon code $couponCode is applied."));
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The "%1" coupon code isn\'t valid. Verify the code and try again.',
                                $this->escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                }
            }
        }

        return $this;
    }

    protected function _initSession()
    {
        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int)$customerId);
        }

        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int)$storeId);
        }

        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string)$currencyId);
            $this->_getOrderCreateModel()->setRecollect(true);
        }
        return $this;
    }

    /**
     * Processing request data
     *
     * @return $this
     */
    protected function _processData()
    {
        return $this->_processActionData();
    }
}
