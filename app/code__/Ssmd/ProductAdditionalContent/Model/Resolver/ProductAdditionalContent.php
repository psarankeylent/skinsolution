<?php

declare(strict_types=1);

namespace Ssmd\ProductAdditionalContent\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ssmd\ProductAdditionalContent\Helper\Output;

class ProductAdditionalContent implements ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface
     */
    protected $output;


    /**
     * Constructor
     *
     * @param ProductIntervalRepositoryInterface $intervalRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Output $output
    ) {
        $this->output = $output;
    }

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

        $productAdditionalContent = json_decode($attrValue);

        $additionalContents = [];
        foreach ($productAdditionalContent as $additionalContent) {

            $additionalContents[] = [
                'content_section' => $additionalContent->content_section,
                'content_html' => [
                    'html' => $this->output->productContentHtml($additionalContent->content_html)
                ]
            ];
        }

        return $additionalContents;
    }
}
