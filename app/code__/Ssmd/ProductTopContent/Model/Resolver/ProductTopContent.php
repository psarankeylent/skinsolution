<?php

declare(strict_types=1);

namespace Ssmd\ProductTopContent\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ssmd\ProductTopContent\Helper\Output;

class ProductTopContent implements ResolverInterface
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

        $productTopContent = json_decode($attrValue);

        $topContents = [];
        foreach ($productTopContent as $topContent) {

            if (isset($topContent->content_section))
                $url = $topContent->content_section[0]->url;
            else
                $url = null;

            $topContents[] = [
                'content_section' => $url,
                'content_html' => [
                    'html' => $this->output->productContentHtml($topContent->content_html)
                ]
            ];
        }

        return $topContents;
    }
}
