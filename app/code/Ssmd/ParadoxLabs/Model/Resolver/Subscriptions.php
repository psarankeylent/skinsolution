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
     * Constructor
     *
     * @param ProductIntervalRepositoryInterface $intervalRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Api\ProductIntervalRepositoryInterface $intervalRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->intervalRepository = $intervalRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $product = $value['model'];

        $subscriptionOptions = $this->intervalRepository->getIntervalsByProductId($product->getEntityId());

        return $subscriptionOptions->getItems();
    }
}
