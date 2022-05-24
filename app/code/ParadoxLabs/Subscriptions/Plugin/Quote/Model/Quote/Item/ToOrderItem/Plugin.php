<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Plugin\Quote\Model\Quote\Item\ToOrderItem;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as AbstractQuoteItem;

/**
 * Plugin for Magento\Quote\Model\Quote\Item\ToOrderItem
 */
class Plugin
{
    /**
     * Transfer items' subscription model from quote to order
     *
     * @param QuoteToOrderItem $subject
     * @param OrderItemInterface $result
     * @param AbstractQuoteItem $item
     * @param array $data
     * @return OrderItemInterface
     */
    public function afterConvert(
        QuoteToOrderItem $subject,
        OrderItemInterface $result,
        AbstractQuoteItem $item,
        $data = []
    ) {
        if ($item->hasData('subscription')) {
            $result->setData('subscription', $item->getData('subscription'));
        }

        return $result;
    }
}
