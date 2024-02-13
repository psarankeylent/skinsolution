<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Observer\Catalog;

use Ssmd\Catalog\Helper\Product;

class ProductSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Product
     */
    private $helper;

    public function __construct(
        \Backend\CatalogTracking\Model\CatalogTrackingFactory  $catalogTrackingFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        Product $helper
    )
    {
        $this->helper       = $helper;
        $this->adminSession = $adminSession;
        $this->catalogTrackingFactory = $catalogTrackingFactory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $product = $observer->getProduct();

        $product->setProductType($this->helper->getProductType($product->getTypeId()));

        //$this->updateProductMiscAttributes($product);
        $this->updateCategoryNameAttributeValue($product);
        $this->udpateSpecialPriceDiscountPercentAttributeValue($product);

        $this->catalogTracking($product);

    }

    /*protected function updateProductMiscAttributes($product)
    {
        $product->setProductType($this->helper->getProductType($product->getTypeId()));
    }*/

    protected function updateCategoryNameAttributeValue($product)
    {
        $previousCategoryIds = is_array($product->getOrigData('category_ids'))?$product->getOrigData('category_ids'):[];
        $currentCategoryIds = is_array($product->getData('category_ids'))?$product->getData('category_ids'):[];
        if (array_diff($previousCategoryIds, $currentCategoryIds) || array_diff($currentCategoryIds, $previousCategoryIds))
            $categoryName = $this->helper->buildCategoryNameAttributeValue($product);
        if (!empty($categoryName))
            $product->setCategoryName($categoryName);
    }

    protected function udpateSpecialPriceDiscountPercentAttributeValue($product)
    {
        $regularPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();
        /*$finalPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)->getAmount()->getValue();

        $msrpPrice = $product->getPriceInfo()->getPrice(\Magento\Msrp\Pricing\Price\MsrpPrice::PRICE_CODE)->getAmount()->getValue();

        if ($finalPrice < $regularPrice) {
            $discountPercentOff = round(100 - $finalPrice / $regularPrice * 100) . '%';
            $product->setDiscountPercent($discountPercentOff);
        } else {
            $product->setDiscountPercent(0);
        }*/

        $msrpPrice = $product->getData('msrp');

        if ($msrpPrice > $regularPrice) {
            $discountPercentOff = round(100 - $regularPrice / $msrpPrice * 100);
            $product->setDiscountPercent($discountPercentOff);

        } else {
            $product->setDiscountPercent(0);
        }


    }

    protected function catalogTracking($product)
    {
        $beforeSaveProduct  = $product->getOrigData();
        $afterSaveProduct   = $product->getData();

        $beforeSaveProduct  = json_encode($beforeSaveProduct);
        $afterSaveProduct  = json_encode($afterSaveProduct);

        $adminUser = $this->adminSession->getUser()->getUsername();

        $savedata = array('identifier' => $product->getSku(), 'user_name' => $adminUser, 'data_before_save' => $beforeSaveProduct, 'data_after_save' => $afterSaveProduct);

        $catalogTracking = $this->catalogTrackingFactory->create();
        $catalogTracking->setData($savedata);
        $catalogTracking->save();

    }


}

