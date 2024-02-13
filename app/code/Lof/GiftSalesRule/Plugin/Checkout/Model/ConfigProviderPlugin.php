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

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Lof\GiftSalesRule\Helper\GiftRule;
use Lof\GiftSalesRule\Helper\Config;

class ConfigProviderPlugin
{
    /**
     * @var GiftRule
     */
    protected $giftRuleHelper;
    protected $_cart;
    protected $giftConfig;
    /**
     * @param GiftRule $giftRuleHelper 
     * @param Config $giftConfig
     * @param \Magento\Checkout\Model\CartFactory $cart     
     */
    public function __construct(
        GiftRule $giftRuleHelper,
        Config $giftConfig,
        \Magento\Checkout\Model\CartFactory $cart
    ) {
        $this->giftRuleHelper = $giftRuleHelper;
        $this->giftConfig = $giftConfig;
        $this->_cart = $cart;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array                                         $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        if($this->giftConfig->isShowNoticeText()){
            $quote = $this->_cart->create()->getQuote();
            $quoteFreegiftNotes = [];
            $quoteFreegiftIcons = [];
            $update_freegift_msg = false;
            $freegifticon = $this->giftConfig->getGiftIcon();
            foreach ($quote->getAllItems() as &$quoteItem) {
                if ($this->giftRuleHelper->isGiftItem($quoteItem)) {
                    $msg = $this->giftConfig->getFreegiftInfoBlock($quoteItem->getProductId(), $quoteItem->getProduct());
                    $quoteItem->setData("freegift_msg",$msg);
                    $quoteItem->setData("freegift_icon", $freegifticon);
                    $quoteFreegiftNotes[$quoteItem->getId()] = $msg;
                    $quoteFreegiftIcons[$quoteItem->getId()] = $freegifticon;
                    $update_freegift_msg = true;
                }
            }
            if($update_freegift_msg){
                $result['quoteFreegiftMessages'] = $quoteFreegiftNotes;
                $result['quoteFreegiftIcons'] = $quoteFreegiftIcons;
                $quote->save();
            }
        }
        return $result;
    }
}