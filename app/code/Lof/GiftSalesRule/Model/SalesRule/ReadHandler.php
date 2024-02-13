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
namespace Lof\GiftSalesRule\Model\SalesRule;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\Rule;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;
use Lof\GiftSalesRule\Model\GiftRuleRepository;

/**
 * Class ReadHandler
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var GiftRuleRepository
     */
    protected $giftRuleRepository;

    /**
     * ReadHandler constructor.
     *
     * @param MetadataPool       $metadataPool       Metadata pool
     * @param GiftRuleRepository $giftRuleRepository Gift rule repository
     */
    public function __construct(
        MetadataPool $metadataPool,
        GiftRuleRepository $giftRuleRepository
    ) {
        $this->metadataPool = $metadataPool;
        $this->giftRuleRepository = $giftRuleRepository;
    }

    /**
     * Fill Sales Rule extension attributes with gift rule attributes
     *
     * @param Rule|object $entity    Entity
     * @param array       $arguments Arguments
     *
     * @return Rule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $attributes = $entity->getExtensionAttributes() ?: [];
        $metadata = $this->metadataPool->getMetadata(RuleInterface::class);
        if ($entity->getData($metadata->getLinkField())) {
            try {
                /** @var GiftRuleInterface $giftRule */
                $giftRule = $this->giftRuleRepository->getById($entity->getData($metadata->getLinkField()));

                $maximumNumber = $giftRule->getMaximumNumberProduct();
                $attributes['gift_rule'][GiftRuleInterface::MAXIMUM_NUMBER_PRODUCT] = $maximumNumber;
                $attributes['gift_rule'][GiftRuleInterface::PRICE_RANGE] = $giftRule->getPriceRange();
            } catch (NoSuchEntityException $exception) {
                $attributes['gift_rule'][GiftRuleInterface::MAXIMUM_NUMBER_PRODUCT] = null;
                $attributes['gift_rule'][GiftRuleInterface::PRICE_RANGE] = null;
            }
        }
        $entity->setExtensionAttributes($attributes);

        return $entity;
    }
}
