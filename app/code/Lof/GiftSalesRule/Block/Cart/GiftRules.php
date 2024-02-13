<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Block\Cart;

use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\View\Element\Template\Context;
use Lof\GiftSalesRule\Api\GiftRuleServiceInterface;

/**
 * Class GiftRules
 *
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class GiftRules extends \Magento\Framework\View\Element\Template
{
    /**
     * @var GiftRuleServiceInterface
     */
    protected $giftRuleService;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * Cart constructor.
     *
     * @param Context                  $context         Context
     * @param GiftRuleServiceInterface $giftRuleService Gift rule service
     * @param CheckoutCart             $cart            Cart
     * @param array                    $data            Data
     */
    public function __construct(
        Context $context,
        GiftRuleServiceInterface $giftRuleService,
        CheckoutCart $cart,
        array $data = []
    ) {
        $this->giftRuleService = $giftRuleService;
        $this->cart = $cart;
        parent::__construct($context, $data);
    }

    /**
     * Get gift rules
     *
     * @return array
     */
    public function getGiftRules()
    {
        return $this->giftRuleService->getAvailableGifts($this->cart->getQuote());
    }
}
