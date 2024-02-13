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
namespace Lof\GiftSalesRule\Model;

use Exception;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface;
use Lof\GiftSalesRule\Api\Data\GiftRuleDataInterfaceFactory;
use Lof\GiftSalesRule\Api\GiftRuleServiceInterface;
use Lof\GiftSalesRule\Helper\Cache as GiftRuleCacheHelper;
use Lof\GiftSalesRule\Helper\Config as GiftConfig;

/**
 * Class GiftRuleService
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class GiftRuleService implements GiftRuleServiceInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var GiftRuleCacheHelper
     */
    protected $giftRuleCacheHelper;

    /**
     * @var GiftRuleDataInterfaceFactory
     */
    protected $giftRuleDataFactory;
    
    protected $giftConfig;

    protected $giftRuleFactory;


    /**
     * GiftRuleService constructor.
     *
     * @param CheckoutSession              $checkoutSession     Checkout session
     * @param Cart                         $cart                Cart
     * @param CacheInterface               $cache               Cache
     * @param GiftRuleCacheHelper          $giftRuleCacheHelper Gift rule cache helper
     * @param GiftRuleDataInterfaceFactory $giftRuleDataFactory Gift rule data factory
     * @param GiftConfig                   $giftConfig
     * @param GiftRuleFactory $giftRuleFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Cart $cart,
        CacheInterface $cache,
        GiftRuleCacheHelper $giftRuleCacheHelper,
        GiftRuleDataInterfaceFactory $giftRuleDataFactory,
        GiftConfig                   $giftConfig,
        GiftRuleFactory $giftRuleFactory
        
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->cache = $cache;
        $this->giftRuleCacheHelper = $giftRuleCacheHelper;
        $this->giftRuleDataFactory = $giftRuleDataFactory;
        $this->giftConfig   = $giftConfig;
        $this->giftRuleFactory = $giftRuleFactory;
    }

    /**
     * Get available gifts
     *
     * @param Quote $quote Quote
     * @param bool $isApi
     * @return GiftRuleDataInterface[]
     */
    public function getAvailableGifts(Quote $quote, $isApi = false)
    {
        /** @var array $gifts */
        $gifts = [];

        /** @var array $quoteItems */
        $quoteItems = [];

        /** @var array $giftRules */
        $giftRules = $this->checkoutSession->getGiftRules();
        /** @var Item $item */
        $gift_items = [];
        foreach ($quote->getAllItems() as $item) {
            /** @var Option $option */
            $option = $item->getOptionByCode('option_gift_rule');

            if ($option) {
                $quoteItems[$option->getValue()][$item->getProductId()] = $item->getQty();
            }
        }
        
        if (is_array($giftRules)) {
            $msg = $this->giftConfig->getNoticeText();
            foreach ($giftRules as $giftRuleId => $giftRuleCode) {
                $giftRuleCachedData = $this->giftRuleCacheHelper->getCachedGiftRule($giftRuleCode);
                if (!$giftRuleCachedData) {
                    continue;
                }

                $gifts[$giftRuleId] = $giftRuleCachedData;
                $gifts[$giftRuleId][GiftRuleDataInterface::NOTICE] = $msg;
                $gifts[$giftRuleId][GiftRuleDataInterface::RULE_ID] = $giftRuleId;
                $gifts[$giftRuleId][GiftRuleDataInterface::CODE] = $giftRuleCode;
                if(!$isApi){
                    $gifts[$giftRuleId][GiftRuleDataInterface::GIFT_ITEMS] = $gift_items;
                }
                $gifts[$giftRuleId][GiftRuleDataInterface::REST_NUMBER]
                    = $gifts[$giftRuleId][GiftRuleDataInterface::NUMBER_OFFERED_PRODUCT];
                $gifts[$giftRuleId][GiftRuleDataInterface::QUOTE_ITEMS] = [];
                if (isset($quoteItems[$giftRuleId])) {
                    $gifts[$giftRuleId][GiftRuleDataInterface::QUOTE_ITEMS] = $quoteItems[$giftRuleId];
                    $gifts[$giftRuleId][GiftRuleDataInterface::REST_NUMBER]
                        -= array_sum($gifts[$giftRuleId][GiftRuleDataInterface::QUOTE_ITEMS]);
                }
                /** @var GiftRuleDataInterface $giftRuleData */
                $giftRuleData = $this->giftRuleDataFactory->create();
                $gifts[$giftRuleId] = $giftRuleData->populateFromArray($gifts[$giftRuleId]);
            }
        }

        return $gifts;
    }

    /**
     * Add gift product
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Products
     * @param string   $identifier Identifier
     * @param int|null $giftRuleId Gift rule id
     *
     * @return mixed|void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addGiftProducts(Quote $quote, array $products, string $identifier, int $giftRuleId = null)
    {
        if ($giftRuleId == null) {
            $giftRuleId = $identifier;
        }

        $giftRuleData = $this->giftRuleCacheHelper->getCachedGiftRule($identifier);
        if (!$giftRuleData) {
            throw new Exception(__('The gift rule is not valid.'));
        }

        foreach ($products as $product) {
            if (!(isset($product['id']) && isset($product['qty']))) {
                throw new Exception(__('We found an invalid request for adding gift product.'));
            }

            if ($this->isAuthorizedGiftProduct($product['id'], $giftRuleData, $product['qty'])) {
                $product['gift_rule'] = $giftRuleId;
                $this->cart->addProduct($product['id'], $product);
            } else {
                throw new Exception(__('We can\'t add this gift item to your shopping cart.'));
            }
        }
    }

    /**
     * Replace gift product
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Product
     * @param string   $identifier Identifier
     * @param int|null $giftRuleId Gift rule id
     * @param \Magento\Quote\Api\CartItemRepositoryInterface|\Magento\Quote\Api\GuestCartItemRepositoryInterface|null $repository
     * @param string|null $cartId
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function replaceGiftProducts(Quote $quote, array $products, string $identifier, int $giftRuleId = null, $repository = null, $cartId = null)
    {
        if ($giftRuleId == null) {
            $giftRuleId = $identifier;
        }

        $giftRuleData   = $this->giftRuleCacheHelper->getCachedGiftRule($identifier);
        if (!$giftRuleData) {
            throw new Exception(__('The gift rule is not valid.'));
        }
        $quoteGiftItems = $this->getQuoteGiftItems($quote, $giftRuleId);

        foreach ($products as $product) {
            if (!(isset($product['id']) && isset($product['qty']))) {
                throw new Exception(__('We found an invalid request for adding gift product.'));
            }

            if ($this->isAuthorizedGiftProduct($product['id'], $giftRuleData, $product['qty'])) {
                $quoteItem = false;

                $productId = $product['id'];
                if (isset($product['super_attribute'])) {
                    $productId = $product['id'].json_encode($product['super_attribute']);
                }

                if (isset($quoteGiftItems[$productId])) {
                    /** @var Item $quoteItem */
                    $quoteItem = $quoteGiftItems[$productId];
                    unset($quoteGiftItems[$productId]);
                }

                if ($quoteItem) {
                    $quoteItem->setQty($product['qty']);
                    $product['gift_rule'] = $giftRuleId;
                    if($repository && isset($product['cart_item'])){
                        $product['cart_item']->setItemId((int)$quoteItem->getItemId());
                        $cart_item = $repository->save($product['cart_item']);
                        if(0 < (float)$cart_item->getPrice() && $cart_item->getItemId()){
                            $this->giftRuleFactory->create()->updateQuoteItemPrice((int)$cart_item->getItemId());
                        }
                        $this->giftRuleFactory->create()->updateQuoteItemOption((int)$cart_item->getItemId(), $product);
                    }
                } else {
                    $product['gift_rule'] = $giftRuleId;
                    if($repository && isset($product['cart_item'])){
                        $cart_item = $repository->save($product['cart_item']);
                        if(0 < (float)$cart_item->getPrice() && $cart_item->getItemId()){
                            $this->giftRuleFactory->create()->updateQuoteItemPrice((int)$cart_item->getItemId());
                        }
                        $this->giftRuleFactory->create()->updateQuoteItemOption((int)$cart_item->getItemId(), $product);
                    }else {
                        $this->cart->addProduct($product['id'], $product);
                    }
                }
            } else {
                throw new Exception(__('We can\'t add this gift item to your shopping cart.'));
            }
        }

        // Remove old gift items.
        if (count($quoteGiftItems) > 0) {
            /** @var Item $quoteGiftItem */
            foreach ($quoteGiftItems as $quoteGiftItem) {
                if($repository && $cartId){
                    $repository->deleteById($cartId, $quoteGiftItem->getId());
                }else {
                    $this->cart->removeItem($quoteGiftItem->getId());
                }
            }
        }
    }

    /**
     * Check if is authorized gift product
     *
     * @param string   $productId    Product id
     * @param array $giftRuleData Gift rule data
     * @param int   $qty          Qty
     *
     * @return bool
     */
    protected function isAuthorizedGiftProduct($productId, $giftRuleData, $qty)
    {
        $isAuthorizedGiftProduct = false;
        if(is_numeric($productId)){
            if (array_key_exists($productId, $giftRuleData[GiftRuleCacheHelper::DATA_PRODUCT_ITEMS])
                && $qty <= $giftRuleData[GiftRuleCacheHelper::DATA_NUMBER_OFFERED_PRODUCT]) {
                $isAuthorizedGiftProduct = true;
            }
        }else {
            if(in_array($productId, $giftRuleData[GiftRuleCacheHelper::DATA_PRODUCT_ITEMS])
            && $qty <= $giftRuleData[GiftRuleCacheHelper::DATA_NUMBER_OFFERED_PRODUCT]) {
                $isAuthorizedGiftProduct = true;
            }
        }
        return $isAuthorizedGiftProduct;
    }

    /**
     * Get quote gift item
     *
     * @param Quote $quote      Quote
     * @param int   $giftRuleId Gift rule id
     *
     * @return array
     */
    protected function getQuoteGiftItems(Quote $quote, int $giftRuleId)
    {
        $quoteItem = [];

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            /** @var Option $option */
            $option = $item->getOptionByCode('option_gift_rule');
            if ($option && $option->getValue() == $giftRuleId) {
                $attributesOptionValue = '';
                /** @var Option $attributesOption */
                $attributesOption = $item->getOptionByCode('attributes');
                if ($attributesOption) {
                    $attributesOptionValue = $attributesOption->getValue();
                }
                $quoteItem[$item->getProductId()  . $attributesOptionValue] = $item;
            }
        }

        return $quoteItem;
    }
}
