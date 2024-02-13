<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Model;

use Magento\Framework\DataObject;
use Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface;
use Lof\GiftSalesRule\Api\Data\CartItemInterfaceFactory;
use Lof\GiftSalesRule\Api\Data\GiftItemInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
/**
 * GiftRuleData model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class GiftRuleData extends DataObject implements GiftRuleDataInterface
{
    protected $cartItemFactory = null;
    protected $giftItemFactory = null;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    public function __construct(
        CartItemInterfaceFactory $cartItemFactory,
        GiftItemInterfaceFactory $giftItemFactory,
        CollectionFactory $collectionFactory,
        array $data = []
     )
    {
        parent::__construct($data);
        $this->cartItemFactory = $cartItemFactory;
        $this->giftItemFactory = $giftItemFactory;
        $this->productCollectionFactory = $collectionFactory;
    }
    /**
     * Get the maximum number product.
     *
     * @return int
     */
    public function getNumberOfferedProduct()
    {
        return $this->getData(self::NUMBER_OFFERED_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setNumberOfferedProduct($numberOfferedProduct)
    {
        return $this->setData(self::NUMBER_OFFERED_PRODUCT, $numberOfferedProduct);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($value)
    {
        return $this->setData(self::RULE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductItems()
    {
        return $this->getData(self::PRODUCT_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductItems($items)
    {
        return $this->setData(self::PRODUCT_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteItems()
    {
        return $this->getData(self::QUOTE_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteItems($items)
    {
        return $this->setData(self::QUOTE_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftQuoteItems()
    {
        return $this->getData(self::GIFT_QUOTE_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftQuoteItems($items)
    {
        return $this->setData(self::GIFT_QUOTE_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getRestNumber()
    {
        return $this->getData(self::REST_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setRestNumber($value)
    {
        return $this->setData(self::REST_NUMBER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotice()
    {
        return $this->getData(self::NOTICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotice($value)
    {
        return $this->setData(self::NOTICE, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getGiftItems()
    {
        return $this->getData(self::GIFT_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftItems(array $items = null)
    {
        return $this->setData(self::GIFT_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromArray(array $values)
    {
        $this->setLabel($values['label']);
        $this->setNumberOfferedProduct($values['number_offered_product']);
        $this->setRestNumber($values['rest_number']);
        $this->setQuoteItems($values['quote_items']);
        $this->setCode($values['code']);
        $this->setRuleId($values['rule_id']);
        $quote_items = array_keys($values['quote_items']);
        $this->setGiftQuoteItems($quote_items);
        if(isset($values['product_items'])){
            $this->setProductItems($values['product_items']);
        }
        if(isset($values['notice'])){
            $this->setNotice($values['notice']);
        }
        if(isset($values['gift_items'])){
            $this->setGiftItems($values['gift_items']);
        }elseif($this->getProductItems()) {
            $product_items = [];
            $products = $this->getProductCollection($this->getProductItems())->getItems();
            $quote_items = $this->getQuoteItems();
            foreach($products as $product){
                $added = false;
                if($quote_items && isset($quote_items[$product->getId()])){
                    $added = true;
                }
                $configurable = false;
                $has_options = false;
                $required_options = false;
                if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
                    $configurable = true;
                }
                if($product->getHasOptions()){
                    $has_options = true;
                }
                if($product->getRequiredOptions()){
                    $required_options = true;
                }
                $image = $product->getSmallImage();
                $product_item = [
                    "id" => $product->getId(),
                    "sku" => $product->getSku(),
                    "name" => $product->getName(),
                    "gift_price" => 0.0000,
                    "free_ship" => true,
                    "added" => $added,
                    "configurable" => $configurable,
                    "has_options" => $has_options,
                    "required_options" => $required_options,
                    "final_price" => $product->getPriceInfo()->getPrice('final_price')->getValue(),
                    "image" => $image
                ];
                $giftItemData = $this->giftItemFactory->create();
                $product_items[$product->getId()] = $giftItemData->populateFromArray($product_item);
            }
            $this->setGiftItems($product_items);
        }
        return $this;
    }

    /**
     * Get product collection.
     *
     * @param array $productItems Product items
     *
     * @return Collection
     */
    public function getProductCollection(array $productItems)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect(['small_image', 'name', 'has_options', 'required_options'])
            ->addIdFilter(array_keys($productItems))
            ->addFinalPrice()
        ;

        return $productCollection;
    }
}
