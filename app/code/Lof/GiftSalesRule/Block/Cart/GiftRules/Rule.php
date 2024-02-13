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

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface;
use Lof\GiftSalesRule\Api\GiftRuleServiceInterface;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;
use Lof\GiftSalesRule\Helper\Config;
/**
 * Class GiftRules
 *
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class Rule extends \Magento\Framework\View\Element\Template
{
    /**
     * @var GiftRuleServiceInterface
     */
    protected $giftRuleService;

    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    protected $giftConfig;
    /**
     * Cart constructor.
     *
     * @param Context                  $context           Context
     * @param GiftRuleServiceInterface $giftRuleService   Gift rule service
     * @param GiftRuleHelper           $giftRuleHelper    Gift rule helper
     * @param CollectionFactory        $collectionFactory Collection factory
     * @param Config $giftConfig
     * @param array                    $data              Data
     */
    public function __construct(
        Context $context,
        GiftRuleServiceInterface $giftRuleService,
        GiftRuleHelper $giftRuleHelper,
        CollectionFactory $collectionFactory,
        Config $giftConfig,
        array $data = []
    ) {
        $this->giftRuleService = $giftRuleService;
        $this->giftRuleHelper = $giftRuleHelper;
        $this->productCollectionFactory = $collectionFactory;
        $this->giftConfig = $giftConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get gift rule
     *
     * @return GiftRuleDataInterface
     */
    public function getGiftRule(): GiftRuleDataInterface
    {
        return $this->getData('gift_rule');
    }

    /**
     * Set gift rule
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
            ->addAttributeToSelect(['small_image', 'name'])
            ->addIdFilter(array_keys($productItems))
            ->addFinalPrice()
        ;

        return $productCollection;
    }

    /**
     * Get add to cart url.
     *
     * @param int    $giftRuleId   Gift rule id
     * @param string $giftRuleCode Gift rule code
     *
     * @return string
     */
    public function getAddToCartUrl($giftRuleId, $giftRuleCode)
    {
        return $this->giftRuleHelper->getAddUrl($giftRuleId, $giftRuleCode);
    }

    /**
     * Get button label.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getButtonLabel()
    {
        $rule = $this->getGiftRule();
        $buttonLabel = $this->giftConfig->getButtonText();
        $buttonLabel = $buttonLabel?$buttonLabel:__("Choose my gift(s)");
        if ($rule->getRestNumber() !== $rule->getNumberOfferedProduct()) {
            $buttonLabel = __('Edit my choices');
        }

        return $buttonLabel;
    }
}
