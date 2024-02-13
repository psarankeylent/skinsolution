<?php

namespace Ssmd\Catalog\Plugin;

use Magento\Framework\Exception\LocalizedException;
 
class FreeGiftPlugin
{
    
    public function beforeAddProduct(\Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/free_giftcart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $formKey = $objectManager->get('\Magento\Framework\Data\Form\FormKey');
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');

        $productId=8;
        $freePoduct = $productRepository->getById($productId);
        try {
    
            $params = array(
                'form_key' => $formKey->getFormKey(),
                'product' => $productId, //product Id
                'qty'   => 1, //quantity of product
                'price' => 0 //product price
            );
            
            //$cart->addProduct($freePoduct, $params); 
            $logger->info('afasdf');
            //$cart->save();             
            $logger->info('Added free gift product to cart '.$freePoduct->getSku());
            //$logger->info('ggggggggggg');

        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
 
        return [$productInfo, $requestInfo];
    }
}

