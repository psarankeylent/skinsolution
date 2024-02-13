<?php

namespace FreeGift\CartRulesFreeGift\Model\Rule\Action\Discount;
use Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount;
class FreeGiftRule extends AbstractDiscount
{
    protected $formKey;
    protected $cartFactory;
    protected $productRepository;
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ){
        $this->formKey = $formKey;
        $this->cart = $cartFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $discountFactory = $objectManager->get('\Magento\SalesRule\Model\Rule\Action\Discount\DataFactory');
       // $formKey = $objectManager->get('\Magento\Framework\Data\Form\FormKey');
        //$cart = $objectManager->get('\Magento\Checkout\Model\CartFactory')->create();
        //$productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/free_giftcart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $discountFactory->create();

        if ($rule->getSimpleAction() == 'add_gift' && !empty($rule->getGiftSku()))
        {
            $sku = $rule->getGiftSku();
            $skuArray = explode(',', $sku);
            $logger->info('sku array '.json_encode($sku));

            $logger->info('Rule Action if '.$rule->getSimpleAction());
            foreach ($skuArray as $sku)
            {
                //Load the product based on Sku
                $product = $this->productRepository->get($sku);
                $qty=1;
                $params = array(
                    'form_key' => $this->formKey->getFormKey(),
                    'product' => $product->getId(),
                    'price' => 0,
                    'qty'   => $qty
                );
                $logger->info('qty '.$qty);
                $cart = $this->cart->create();
                $cart->addProduct($product, $params);
                $cart->save();

            }
        }
        else{
           // $logger->info('Rule Action else '.$rule->getSimpleAction());
            $logger->info('Skus Empty. Please insert any product sku to add for Free Gift. '.$rule->getGiftSku());
        }

        return $discountData;
    }
}
