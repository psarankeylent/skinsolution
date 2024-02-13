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
use Lof\GiftSalesRule\Helper\GiftRule;

/**
 * Class SetGiftItemPrice
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class SetGiftItemPrice implements ObserverInterface
{
    /**
     * @var GiftRule
     */
    protected $giftRuleHelper;

    /**
     * @param GiftRule $giftRuleHelper gift rule helper
     */
    public function __construct(GiftRule $giftRuleHelper)
    {
        $this->giftRuleHelper = $giftRuleHelper;
    }

    /**
     * Change price for gift product
     *
     * @param Observer $observer Observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getData('quote_item');
        if ($this->giftRuleHelper->isGiftItem($item)) {
            $item->setCustomPrice(0);
            $item->setOriginalCustomPrice(0);
        }
    }
}
