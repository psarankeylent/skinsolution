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
namespace Lof\GiftSalesRule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class AddGiftRuleOption
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class AddGiftRuleOption implements ObserverInterface
{
    /**
     * Add option for gift item
     *
     * @event checkout_cart_save_after
     *
     * @param Observer $observer Observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\DataObject $buyRequest */
        $buyRequest = $observer->getEvent()->getBuyRequest();

        $giftRuleId = $buyRequest->getData('gift_rule');
        if ($giftRuleId) {
            $transport = $observer->getEvent()->getTransport();
            $transport->options['gift_rule'] = $giftRuleId;
        }
    }
}
