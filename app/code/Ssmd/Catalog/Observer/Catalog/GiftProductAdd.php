<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Observer\Catalog;

use Ssmd\Catalog\Helper\Product;

class GiftProductAdd implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;
    protected $formKey;
    protected $cart;
    protected $productRepositoryInterface;

    public function __construct(
        Product $helper,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
    )
    {
        $this->helper = $helper;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->productRepository = $productRepositoryInterface;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/free_giftcart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$logger->info('Adding free gift');
       
        $item = $observer->getEvent()->getData('quote_item');
        
        /*
        $logger->info('Adding free gift');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $formKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        $cart = $objectManager->get('Magento\Checkout\Model\Cart');
        $productRepository = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');

        $productId=8;
        $product = $productRepository->getById(8);
        $params = array(
                'form_key' => $formKey->getFormKey(),
                'product' => $productId, //product Id
                'qty'   => 1, //quantity of product
                'price' => 0 //product price
            );
        $cart->addProduct($product, $params); 
        $logger->info('Added Free Gift 8');*/
    }

    public function addFreeGiftToCart($sku)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/free_giftcart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);



        $product = $this->productRepository->get($sku);
        
        $freeGiftSkuArr = $product->getData('my_free_gift');
       // $productsArr = explode(',', $freeGiftSkuArr);

        
                $logger->info('Free Product Sku '.$freeGiftSkuArr);

                $freePoduct = $this->productRepository->get($freeGiftSkuArr);
                $productId = $freePoduct->getId();
                $logger->info('Free Product id '.$productId);

                $params = array(
                    'form_key' => $this->formKey->getFormKey(),
                    'product' => $productId, //product Id
                    'qty'   => 1, //quantity of product
                    'price' => 0 //product price
                );

                $this->cart->addProduct($productId, $freePoduct);
                //$this->cart->addProduct($freePoduct, $params); 
                $logger->info('afasdf');
                //$this->cart->save();              
                $logger->info('Added free gift product to cart '.$freePoduct->getSku());
                $logger->info('ggggggggggg');

                
    }
}

