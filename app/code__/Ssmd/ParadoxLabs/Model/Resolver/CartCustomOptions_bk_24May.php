<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ParadoxLabs\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\QuoteGraphQl\Model\Resolver\CartItems;
use ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface;

/**
 * Adds custom_options to the cart items interface
 */
class CartCustomOptions implements ResolverInterface
{
    /**
     * Constructor
     *
     * @param ProductIntervalRepositoryInterface $intervalRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $productCustomOption
    ) {
        $this->productCustomOption = $productCustomOption;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('Exception'));
        }
        $cartItem = $value['model'];

        return $this->getCustomOptions($cartItem);
    }

    protected function getCustomOptions($cartItem)
    {
        $customOptions = $cartItem->getProduct()->getCustomOptions();

        if (array_key_exists('info_buyRequest',$customOptions)) {
            $customOptions = $customOptions['info_buyRequest']->getData('value');
        } else {
            return [];
        }

        $options = [];
        $customOptions = json_decode($customOptions);

        $product = $cartItem->getProduct();
        $productCustomOptions = $this->productCustomOption
            ->getProductOptionCollection($product);

        $customOptionsData = [];
        foreach($productCustomOptions as $option) {
            $values = $option->getValues();
            if (empty($values)) {
                continue;
            }

            foreach($values as $value) {
                $valueData = $value->getData();
                $customOptionsData[$valueData['option_type_id']] = $valueData;
            }
        }

        if (isset($customOptions->options))
	    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customoptions.log');
        	$logger = new \Zend\Log\Logger();
        	$logger->addWriter($writer);
        	$logger->info("customOptions");
        	$logger->info("string value :".json_encode($customOptions));
            foreach ($customOptions->options as $key => $value) {
        	$logger->info("Key : ".json_encode($key));
        	$logger->info("Value: ".!is_array($value)." --");
        	if(!is_array($value)){
		 // $logger->info("Value is empty");	
		  //$logger->info($value);	
		  return;
		}
                $options[] = [ 'id' => $key, 'value' => $value, 'title' => $customOptionsData[$value]['title']];
            }
$logger->info("Options : ".json_encode($options));
        return $options;

    }
}
