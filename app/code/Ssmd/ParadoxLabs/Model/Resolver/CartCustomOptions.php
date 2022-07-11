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

/**
 * Adds custom_options to the cart items interface
 */
class CartCustomOptions implements ResolverInterface
{
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
        $customOptions = $cartItem->getProduct()->getCustomOptions()['info_buyRequest']->getData('value');

        $options = [];
        $customOptions = json_decode($customOptions);


        if (isset($customOptions->options))
            foreach ($customOptions->options as $key => $value) {
                $options[] = [ 'id' => $key, 'value' => $value];
            }

        return $options;
    }
}
