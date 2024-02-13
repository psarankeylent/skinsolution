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

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Data\Collection\AbstractDb as AbstractCollection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractResourceModel;
use Magento\Framework\Phrase;
use Lof\GiftSalesRule\Api\GiftRuleServiceInterface;
use Lof\GiftSalesRule\Api\ProductGiftInterface;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterfaceFactory;
use Lof\GiftSalesRule\Api\Data\AddGiftItemInterfaceFactory;
use Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterface;
use Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterfaceFactory;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Lof\GiftSalesRule\Api\Data\GiftRuleSearchResultsInterfaceFactory;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;
use Lof\GiftSalesRule\Helper\Cache as GiftRuleCacheHelper;
use Lof\GiftSalesRule\Model\ResourceModel\GiftRule as GiftRuleResource;
use Lof\GiftSalesRule\Model\ResourceModel\GiftRule\CollectionFactory as GiftRuleCollectionFactory;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteRepository\LoadHandler;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Cart\CartTotalRepository;
/**
 * GiftRule repository.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductGift implements ProductGiftInterface
{
    /**
     * @var LoadHandler
     */
    private $loadHandler;

    protected $quotesById = [];
    /** @var mixed */
    protected $objectFactory;

    /** @var AbstractResourceModel */
    protected $objectResource;

    /** @var mixed */
    protected $objectCollectionFactory;

    /** @var mixed */
    protected $objectSearchResultsFactory;

    /** @var string|null */
    protected $identifierFieldName = null;

    /** @var array */
    protected $cacheById = [];

    /** @var CacheInterface */
    protected $cache;

    protected $giftRuleService;

    protected $cartFactory;

    protected $storeManager;

    /** @var array */
    protected $cacheByIdentifier = [];

    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    protected $repository;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    protected $cartItemInterfaceFactory;

    protected $productOptionInterfaceFactory;

    protected $addGiftRuleDataInterfaceFactory;

    protected $guestRepository;

    /**
     * GiftRuleRepository constructor.
     *
     * @param GiftRuleInterfaceFactory              $objectFactory              Gift rule interface factory
     * @param GiftRuleResource                      $objectResource             Gift rule resource
     * @param GiftRuleCollectionFactory             $objectCollectionFactory    Gift rule collection factory
     * @param GiftRuleSearchResultsInterfaceFactory $objectSearchResultsFactory Gift rule search results interface factory
     * @param GiftRuleServiceInterface              $giftRuleService
     * @param CartInterfaceFactory                  $cartFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheInterface                        $cache                      Cache interface
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $repository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory
     * @param \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionInterfaceFactory
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterfaceFactory $addGiftRuleDataInterfaceFactory
     * @param null                                  $identifierFieldName        Identifier field name
     */
    public function __construct(
        GiftRuleInterfaceFactory $objectFactory,
        GiftRuleResource $objectResource,
        GiftRuleCollectionFactory $objectCollectionFactory,
        GiftRuleSearchResultsInterfaceFactory $objectSearchResultsFactory,
        GiftRuleServiceInterface $giftRuleService,
        CartInterfaceFactory                  $cartFactory,
        StoreManagerInterface $storeManager,
        CacheInterface $cache,
        \Magento\Quote\Api\CartItemRepositoryInterface $repository,
        \Magento\Quote\Api\GuestCartItemRepositoryInterface $guestRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterfaceFactory,
        \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionInterfaceFactory,
        \Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterfaceFactory $addGiftRuleDataInterfaceFactory,
        $identifierFieldName = null
    ) {
        $this->objectFactory              = $objectFactory;
        $this->objectResource             = $objectResource;
        $this->objectCollectionFactory    = $objectCollectionFactory;
        $this->objectSearchResultsFactory = $objectSearchResultsFactory;
        $this->cache                      = $cache;
        $this->identifierFieldName        = $identifierFieldName;
        $this->giftRuleService            = $giftRuleService;
        $this->cartFactory               = $cartFactory;
        $this->storeManager = $storeManager;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->repository = $repository;
        $this->quoteRepository = $quoteRepository;
        $this->cartItemInterfaceFactory = $cartItemInterfaceFactory;
        $this->productOptionInterfaceFactory = $productOptionInterfaceFactory;
        $this->addGiftRuleDataInterfaceFactory = $addGiftRuleDataInterfaceFactory;
        $this->guestRepository = $guestRepository;
    }

    /**
     * @inheritdoc
     */
    protected function loadQuote($loadMethod, $loadField, $identifier, array $sharedStoreIds = [])
    {
        /** @var CartInterface $quote */
        $quote = $this->cartFactory->create();
        if ($sharedStoreIds && is_callable([$quote, 'setSharedStoreIds'])) {
            $quote->setSharedStoreIds($sharedStoreIds);
        }
        $quote->setStoreId($this->storeManager->getStore()->getId())->$loadMethod($identifier);
        if (!$quote->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }

    /**
     * @inheritdoc
     */
    public function getGiftsByQuoteId($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->quoteRepository->get($quoteIdMask->getQuoteId());
        if (!$quote->getId()) {
            // Object does not exist.
            throw NoSuchEntityException::singleField('cartId', $cartId);
        }else {
            $quote->collectTotals();
        }
        $gifts = $this->giftRuleService->getAvailableGifts($quote, true);
        
        return $gifts;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGiftByQuoteItemId($cartId, $itemId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->repository->deleteById($quoteIdMask->getQuoteId(), $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function addGift( $addGiftItem)
    {
        $object = $this->addGiftRuleDataInterfaceFactory->create();
        try {
            $cartId = $addGiftItem->getCartId();
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $quote = $this->quoteRepository->get($quoteIdMask->getQuoteId());
            if (!$quote->getId()) {
                // Object does not exist.
                throw NoSuchEntityException::singleField('cartId', $cartId);
            }else {
                $quote->collectTotals();
            }
            if(!$this->validatePostParameters($addGiftItem)){
                $msg = "Invalid gifts. We can\'t add this gift item to your shopping cart.";
                throw new CouldNotSaveException($msg);
            }
            $params = [];
            $params["cart_id"] = $cartId;
            $params["quote_id"] = $quote->getId();
            $gift_rule_id = $addGiftItem->getGiftRuleId();
            $gift_rule_code = $addGiftItem->getGiftRuleCode();
            $params["products"] = $addGiftItem->getProducts();
            $params["gift_rule_id"] = $gift_rule_id;
            $params["gift_rule_code"] = $gift_rule_code;
            $productData = $this->formatProductPostParameters($params);
            if($productData){
                $this->giftRuleService->replaceGiftProducts(
                    $quote,
                    $productData,
                    $gift_rule_code,
                    $gift_rule_id,
                    $this->guestRepository,
                    $cartId
                );
                $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
                $quote = $this->quoteRepository->get($quoteIdMask->getQuoteId());
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
                $product_gift_id = [];
                foreach($productData as $_product){
                    $product_gift_id[] = (int)$_product['id'];
                }
                $returnData = [
                    "status" => "success",
                    "message" => "You added gift product to your shopping cart.",
                    "gift_rule_id" => $gift_rule_id,
                    "quote_id" => $params["quote_id"],
                    "quote_item_id" => $quote->getLastAddedItem()->getItemId(),
                    "product_gift_id" => $product_gift_id
                ];
                $object->populateFromArray($returnData);
            }
        } catch (\Exception $e) {
            $msg = new Phrase($e->getMessage());
            throw new CouldNotSaveException($msg);
        }

        return $object;
    }


    /**
     * @inheritdoc
     */
    public function getCustomerGiftsByQuoteId($cartId, array $sharedStoreIds = [])
    {
        if (!isset($this->quotesById[$cartId])) {
            $quote = $this->loadQuote('loadByIdWithoutStore', 'cartId', $cartId, $sharedStoreIds);
            $this->getLoadHandler()->load($quote);
            $quote->collectTotals();
            $this->quotesById[$cartId] = $quote;
        }else {
            $quote =$this->quotesById[$cartId];
        }
        $gifts = $this->giftRuleService->getAvailableGifts($quote, true);
        if (!$quote->getId()) {
            // Object does not exist.
            throw NoSuchEntityException::singleField('cartId', $cartId);
        }
        return $gifts;
    }

    
    /**
     * {@inheritdoc}
     */
    public function deleteCustomerGiftByQuoteItemId($cartId, $itemId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('The %1 Cart doesn\'t contain the %2 item.', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("The item couldn't be removed from the quote."));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerGift( $cartId, $addGiftItem, array $sharedStoreIds = [])
    {
        $object = $this->addGiftRuleDataInterfaceFactory->create();
        try {
            if (!isset($this->quotesById[$cartId])) {
                $quote = $this->loadQuote('loadByIdWithoutStore', 'cartId', $cartId, $sharedStoreIds);
                $this->getLoadHandler()->load($quote);
                $quote->collectTotals();
                $this->quotesById[$cartId] = $quote;
            }else {
                $quote =$this->quotesById[$cartId];
            }
            if(!$this->validatePostParameters($addGiftItem)){
                $msg = "Invalid gifts. We can\'t add this gift item to your shopping cart.";
                throw new CouldNotSaveException($msg);
            }
            $params = [];
            $params["quote_id"] = $quote->getId();
            $gift_rule_id = $addGiftItem->getGiftRuleId();
            $gift_rule_code = $addGiftItem->getGiftRuleCode();
            $params["products"] = $addGiftItem->getProducts();
            $params["gift_rule_id"] = $gift_rule_id;
            $params["gift_rule_code"] = $gift_rule_code;
            $productData = $this->formatProductPostParameters($params);
            if($productData){
                $this->giftRuleService->replaceGiftProducts(
                    $quote,
                    $productData,
                    $gift_rule_code,
                    $gift_rule_id,
                    $this->repository,
                    $cartId
                );
                $quote = $this->loadQuote('loadByIdWithoutStore', 'cartId', $cartId, $sharedStoreIds);
                $this->getLoadHandler()->load($quote);
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
                $product_gift_id = [];
                foreach($productData as $_product){
                    $product_gift_id[] = (int)$_product['id'];
                }
                $returnData = [
                    "status" => "success",
                    "message" => "You added gift product to your shopping cart.",
                    "gift_rule_id" => $gift_rule_id,
                    "quote_id" => $params["quote_id"],
                    "quote_item_id" => $quote->getLastAddedItem()->getItemId(),
                    "product_gift_id" => $product_gift_id
                ];
                $object->populateFromArray($returnData);
            }
        } catch (\Exception $e) {
            $msg = new Phrase($e->getMessage());
            throw new CouldNotSaveException($msg);
        }

        return $object;
    }
    /**
     * Get load handler instance.
     *
     * @return LoadHandler
     * @deprecated 100.1.0
     */
    private function getLoadHandler()
    {
        if (!$this->loadHandler) {
            $this->loadHandler = ObjectManager::getInstance()->get(LoadHandler::class);
        }
        return $this->loadHandler;
    }

    protected function validatePostParameters($addGiftItem): bool
    {
        $return = true;
        if (!$addGiftItem || !$addGiftItem->getGiftRuleCode()
            || !$addGiftItem->getGiftRuleId()
            || !$addGiftItem->getProducts()) {
            $return = false;
        }

        if ($return) {
            $products = $addGiftItem->getProducts();
            if($products && is_array($products)){
                foreach ($products as $productData) {
                    if (!$productData || !$productData->getQty()) {
                        $return = false;
                    }
                }
            }else {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Format post parameters for the add to cart method.
     */
    protected function formatProductPostParameters($params)
    {
        $filteredParams = [];
        $quote_id = $params['quote_id'];
        $cart_id = isset($params['cart_id'])?$params['cart_id']:"";
        foreach ($params['products'] as $productData) {
            $productId = $productData->getSku();
            $qty = (int)$productData->getQty();
            if ($qty) {
                $cartItem = $this->cartItemInterfaceFactory->create();
                $cartItem->setQty($qty);
                $cartItem->setPrice(0.0000);
                if($cart_id){
                    $cartItem->setQuoteId($cart_id);
                }else {
                    $cartItem->setQuoteId($quote_id);
                }
                
                $cartItem->setSku($productId);
                if($productData->getProductOption()){
                    $cartItem->setProductOption($productData->getProductOption());
                }

                $productItem = $productData->getData();
                $productItem['uenc'] = $productData->getUenc();
                $productItem['id'] = $productId;
                $productItem['sku'] = $productId;
                $productItem['product'] = $productId;
                $productItem['item'] = $productId;
                $productItem['cart_item'] = $cartItem;
                $filteredParams[] = $productItem;
            }
        }
        return $filteredParams;
    }
}
