<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;

class Product extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * @param $product
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function buildCategoryNameAttributeValue($product)
    {
        foreach($product->getCategoryIds() as $categoryId) {
            if ($categoryId <= 2)
                continue;
            $categoryName[] = $this->categoryRepository->get($categoryId)->getName();
        }

        if (isset($categoryName))
            return implode(', ', $categoryName);

        return null;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateProductCategoryNameAttribute($product)
    {
        try {
            $categoryName = $this->buildCategoryNameAttributeValue($product);

            if (!empty($categoryName))
                $product->setCategoryName(implode(', ', $categoryName))->save();

        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateAllProductsCategoryNameAttribute()
    {
        try {
            $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());
            foreach ($products->getItems() as $product) {
                $this->updateProductCategoryNameAttribute($product);
            }
        } catch (Exception $e) {
            return;
        }
    }
}

