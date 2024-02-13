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
 * @copyright 2020 Lof
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;
use Lof\GiftSalesRule\Model\GiftRule;

/**
 * Class SetNumberOfferedProduct
 *
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class SetNumberOfferedProduct implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * SetNumberOfferedProduct constructor.
     *
     * @param CheckoutSession         $checkoutSession Checkout session
     * @param GiftRuleHelper          $giftRuleHelper  Gift rule config helper
     * @param CartRepositoryInterface $quoteRepository Quote repository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        GiftRuleHelper $giftRuleHelper,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->giftRuleHelper = $giftRuleHelper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Set number offered product after load the gift rule.
     *
     * @param Observer $observer Oberver
     */
    public function execute(Observer $observer)
    {
        /** @var GiftRule $giftRule */
        $giftRule = $observer->getEvent()->getData('data_object');
        $giftRule->setNumberOfferedProduct($giftRule->getMaximumNumberProduct());
        try {
            $quote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());
            if (floatval($giftRule->getPriceRange()) > 0) {
                $range = $this->giftRuleHelper->getRange($quote, $giftRule);
                $giftRule->setNumberOfferedProduct($giftRule->getMaximumNumberProduct() * $range);
            }
        } catch (LocalizedException $localizedException) {
            // In this case, we do nothing.
        }
    }
}
