<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use Magento\Eav\Api\AttributeRepositoryInterface;

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
     * @var AttributeRepositoryInterface
     */
    protected $eavAttributeRepositoryInterface;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        AttributeRepositoryInterface $eavAttributeRepositoryInterface

    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->eavAttributeRepositoryInterface = $eavAttributeRepositoryInterface;

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

            if (!empty($categoryName)) {
                $product->setCategoryName($categoryName)->save();
            }

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

    public function updateProductTypeAttribute()
    {
        try {
            $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());

            foreach ($products->getItems() as $product) {
                $product->setProductType($this->getProductType($product->getTypeId()))
                    ->save();
            }
        } catch (Exception $e) {
            return;
        }
    }

    public function getProductType($attrVal)
    {
        $attribute = $this->eavAttributeRepositoryInterface->get(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_type'
        );

        $options = $attribute->getSource()->getAllOptions();

        $values = [];
        foreach ($options as $option)
        {
            $values[$option['label']->getText()] = $option['value'];
        }

        return $values[$attrVal];
    }
}

