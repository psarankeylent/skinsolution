<?php

declare(strict_types=1);

namespace Ssmd\Catalog\Model\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Api\AttributeRepositoryInterface;

class MapProductAttributeKeyValue implements ResolverInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    protected $eavAttributeRepositoryInterface;

    public function __construct(
        AttributeRepositoryInterface $eavAttributeRepositoryInterface    ) {
        $this->eavAttributeRepositoryInterface = $eavAttributeRepositoryInterface;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!array_key_exists('model', $value) || !$value['model'] instanceof ProductInterface) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        // TODO

        $productAttr = $this->mapAttributeName($field->getName());
        $productValue = $value['model']->getData($productAttr);

        if (is_null($productValue))
            return null;

        return $this->getAttributeOptionValue($productAttr, $productValue );
    }

    protected function mapAttributeName($attr)
    {
        switch ($attr) {
            case 'brand_name':
                $attr = 'brand';
                break;
        }
        return $attr;
    }

    protected function getAttributeOptionValue($attr, $attrValue)
    {
        $attrValues = explode(',', $attrValue);

        $attribute = $this->eavAttributeRepositoryInterface->get(
            \Magento\Catalog\Model\Product::ENTITY,
            $attr
        );

        $options = $attribute->getSource()->getAllOptions();

        $values = [];
        foreach ($options as $option)
        {
            $values[$option['value']] = $option['label']->getText();
        }

        $resolvedValues = [];
        foreach ($attrValues as $attrValue)
        {
            $resolvedValues[] = $values[$attrValue];
        }

        return implode(',', $resolvedValues);
    }
}
