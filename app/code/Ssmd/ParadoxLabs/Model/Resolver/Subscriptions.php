<?php

declare(strict_types=1);

namespace Ssmd\ParadoxLabs\Model\Resolver;

use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ExtractDataFromCategoryTree;
use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\CategoryTree;
use Magento\CatalogGraphQl\Model\Category\CategoryFilter;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

use Magento\Catalog\Model\Product;
use ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface;

/**
 * Product Subscription Options List resolver, used for GraphQL product subscription data request processing.
 */
class Subscriptions implements ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface
     */
    protected $intervalRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Product\Option
     */
    protected $productCustomOption;

    /**
     * Constructor
     *
     * @param ProductIntervalRepositoryInterface $intervalRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Option $productCustomOption
    ) {
        $this->intervalRepository = $intervalRepository;
        $this->productRepository = $productRepository;
        $this->productCustomOption = $productCustomOption;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $product = $value['model'];

        $subscriptionOptions = $this->intervalRepository->getIntervalsByProductId($product->getEntityId());

        $customOptions = $this->productCustomOption
            ->getProductOptionCollection($product);

        $customOptionsData = [];
        foreach($customOptions as $option) {
            $values = $option->getValues();
            if (empty($values)) {
                continue;
            }

            foreach($values as $value) {
                $valueData = $value->getData();
                $customOptionsData[$valueData['option_type_id']] = $valueData;
            }
        }

        $items = [];
        foreach ($subscriptionOptions->getItems() as $subscriptionOption) {
            $item = $subscriptionOption->getData();
            $item['title'] = $customOptionsData[$item['value_id']]['title'];
            $items[] = $item;
        }

        return $items;
    }
}
