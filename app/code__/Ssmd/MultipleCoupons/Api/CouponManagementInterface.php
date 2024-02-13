<?php

namespace Ssmd\MultipleCoupons\Api;

interface CouponManagementInterface
    extends \Magento\Quote\Api\CouponManagementInterface
{

    /**
     * Deletes the $couponCode from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
     public function removeCoupon($cartId, $couponCode);
}
