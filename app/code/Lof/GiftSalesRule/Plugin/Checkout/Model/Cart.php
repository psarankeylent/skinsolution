<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Plugin\Checkout\Model;

use Magento\Checkout\Model\Cart as Subject;
use Magento\Sales\Model\Order\Item;

/**
 * Class Cart
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class Cart
{
    /**
     * Avoid to add a gift product when adding an order item.
     *
     * @param Subject  $subject   Subject
     * @param \Closure $proceed   Parent method
     * @param Item     $orderItem Order item
     * @param bool     $qtyFlag   Quantity flag
     *
     * @return Subject
     */
    public function aroundAddOrderItem(
        Subject $subject,
        \Closure $proceed,
        $orderItem,
        $qtyFlag = null
    ) {
        $info = $orderItem->getProductOptionByCode('info_buyRequest');
        if (isset($info['gift_rule']) && $info['gift_rule']) {
            return $subject;
        }

        return $proceed($orderItem, $qtyFlag);
    }
}
