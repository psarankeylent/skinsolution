<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ssmd\MultipleCoupons\Model;

use Magento\Framework\Exception\LocalizedException;
use \Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement extends \Magento\Quote\Model\CouponManagement
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $coupon;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\SalesRule\Model\Coupon $coupon
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
    )
    {
        $this->coupon = $coupon;
        $this->ruleRepository = $ruleRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param $couponCode
     * @return boolean
     */
    public function isStackableCoupon($couponCode)
    {
         $rule = $this->coupon->loadByCode($couponCode);

         if ($rule && $rule->getRuleId()) {
             $salesRule = $this->ruleRepository->getById($rule->getRuleId());
             $data = $salesRule->__toArray();
                return $data['is_stackable_coupon'];
        }

        return true;
    }

    public function removeAllCouponsOnTheCart($cartId, $couponCodes)
    {
        foreach ($couponCodes as $couponCode)
            $this->removeCoupon($cartId, $couponCode);
    }

    public function quoteHasNonStackableCoupon($couponCodes)
    {
        $flag = false;
        foreach ($couponCodes as $couponCode) {
            $flag = $this->isStackableCoupon($couponCode);
            if (!$flag)
                return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        $oldCouponCode = $quote->getCouponCode();
        $oldCouponCodes = $newCouponCodes = $oldCouponCode ? explode(',', $oldCouponCode) : [];

        if (in_array($couponCode, $oldCouponCodes)) {
            throw new NoSuchEntityException(__("The coupon code $couponCode is already applied."));
        }

        $quoteHasNonStackableCoupon = $this->quoteHasNonStackableCoupon($oldCouponCodes);

        if ($quoteHasNonStackableCoupon) {
            throw new NoSuchEntityException(__("The coupon code $couponCode couldn't be applied. Verify the coupon code and try again."));
        }

        $isStackableCoupon = $this->isStackableCoupon($couponCode);

        if ($isStackableCoupon) {
            $newCouponCodes[] = $couponCode;
            $newCouponCode = implode(',', $newCouponCodes);
        } else {
            //$this->removeAllCouponsOnTheCart($cartId, $oldCouponCodes);
            $newCouponCodes = [$couponCode];
            $newCouponCode = implode(',', $newCouponCodes);
        }

        try {
            $quote->setCouponCode($newCouponCode);
            $this->quoteRepository->save($quote->collectTotals());
            $quoteCouponCodes = explode(',', $quote->getCouponCode());
            $couponCodes = explode(',', $couponCode);
            $invalidCouponCodes = array_diff($couponCodes, $quoteCouponCodes);
            $invalidCouponCode = implode(',', $invalidCouponCodes);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('The coupon code couldn\'t be applied: ' . $e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
                $e
            );
        }

        if (end($quoteCouponCodes) != $couponCode) {
            throw new NoSuchEntityException(__("The coupon code '$couponCode' isn't valid. Verify the code and try again."));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function removeCoupon($cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        $oldCouponCode = $quote->getCouponCode();
        $oldCouponCodes = $newCouponCodes = explode(',', $oldCouponCode);

        if (($key = array_search($couponCode, $oldCouponCodes)) !== false) {
            unset($newCouponCodes[$key]);
        } else {
            throw new NoSuchEntityException(
                __("The coupon code $couponCode couldn't be deleted. Verify the coupon code and try again.")
            );
        }

        $newCouponCode = implode(',', $newCouponCodes);

        try {
            $quote->setCouponCode($newCouponCode);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __($e->getMessage() . "...1 ... The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }

        $appliedCouponCodes = explode(',', $quote->getCouponCode());
        $removedCouponCode = implode(',', array_diff($oldCouponCodes, $appliedCouponCodes));

        if ($removedCouponCode != $couponCode) {
            throw new CouldNotDeleteException(
                __(" ... 2 ...The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        return true;
    }
}
