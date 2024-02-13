<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Block\Cart\GiftRules;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Swatches\Helper\Data;
use Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;

/**
 * Class GiftRules
 *
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class ProductItem extends AbstractProduct
{
    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * @var Data
     */
    protected $swatchHelper;

    /**
     * Cart constructor.
     *
     * @param Context $context      Context
     * @param Data    $swatchHelper Swatch helper
     * @param array   $data         Data
     */
    public function __construct(
        Context $context,
        Data $swatchHelper,
        array $data = []
    ) {
        $this->swatchHelper = $swatchHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get product.
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->getData('product');
    }

    /**
     * Set product.
     *
     * @param Product $product Product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        return $this->setData('product', $product);
    }

    /**
     * Get gift rule.
     *
     * @return GiftRuleDataInterface
     */
    public function getGiftRule(): GiftRuleDataInterface
    {
        return $this->getData('gift_rule');
    }

    /**
     * Set gift rule.
     *
     * @param GiftRuleDataInterface $giftRule Gift rule
     *
     * @return $this
     */
    public function setGiftRule(GiftRuleDataInterface $giftRule)
    {
        return $this->setData('gift_rule', $giftRule);
    }

    /**
     * Get details renderer. Either render swatch or select attributes for configurable products.
     *
     * @param string $type Type
     *
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }

        if ($type === Configurable::TYPE_CODE) {
            $swatchAttributesData = $this->getSwatchAttributesData();
            if (empty($swatchAttributesData)) {
                return $this->getChildBlock('configurable-details');
            } else {
                return $this->getChildBlock('swatch-details');
            }
        }

        return parent::getDetailsRenderer($type);
    }

    /**
     * Get selected product quantity.
     *
     * @return int
     */
    public function getProductQty()
    {
        $qty = 0;
        $productId = $this->getProduct()->getId();
        $quoteFreeItems = $this->getGiftRule()->getQuoteItems();
        if (isset($quoteFreeItems[$productId])) {
            $qty = $quoteFreeItems[$productId];
        }

        return $qty;
    }

    /**
     * Get swatch attributes data.
     *
     * @return array
     */
    protected function getSwatchAttributesData()
    {
        return $this->swatchHelper->getSwatchAttributesAsArray($this->getProduct());
    }
}
