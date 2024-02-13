<?php
declare(strict_types=1);

namespace Ssmd\FreeGift\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;

class FreeGift implements \Magento\Framework\Event\ObserverInterface
{
    const FREE_GIFT_PRODUCT_ID = 11;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    public function __construct(
        ProductRepositoryInterface $productRepositoryInterface
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $cart = $observer->getCart();
        $quote = $cart->getQuote();
        $subtotal = $quote->getData('subtotal');

        $itemId = $this->hasFreeGift($quote);

        if ($subtotal >= 100 && is_null($itemId)) {
            $this->addFreeGift($cart);
        } else if ($subtotal < 100 && !is_null($itemId)) {
            $this->removeFreeGift($cart, $itemId);
        }
    }

    protected function addFreeGift($cart)
    {
        $product = $this->productRepositoryInterface->getById(self::FREE_GIFT_PRODUCT_ID);

        $params = [];
        $params['qty'] = 1;
        $params['product'] = self::FREE_GIFT_PRODUCT_ID;

        $cart->addProduct($product, $params);
        $cart->save();
    }

    protected function removeFreeGift($cart, $itemId)
    {
        $cart->removeItem($itemId)->save();
    }

    protected function hasFreeGift($quote)
    {
        $items = $quote->getAllVisibleItems();

        foreach ($items as $item) {
            if ($item->getProductId() == self::FREE_GIFT_PRODUCT_ID) {
                return $item->getId();
            }
        }
        return null;
    }
}
