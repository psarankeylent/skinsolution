<?php

declare(strict_types=1);

namespace Ssmd\ProductFaqs\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductFaqs implements ResolverInterface
{
   /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('Exception'));
        }

        $attrValue = $value['model']->getData($field->getName());

        if (is_null($attrValue))
            return null;

        $productFaqs = json_decode($attrValue);

        $faqs = [];
        foreach ($productFaqs as $faq) {
            $faqs[] = ['question' => $faq->question, 'answer' => $faq->answer];
        }

        return $faqs;
    }
}
