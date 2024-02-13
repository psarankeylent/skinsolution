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
namespace Lof\GiftSalesRule\Plugin\Model;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\SalesRule\Api\Data\RuleExtensionInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;
use Lof\GiftSalesRule\Api\GiftRuleRepositoryInterface;
use Lof\GiftSalesRule\Model\GiftRuleFactory;

/**
 * Rule Repository Plugin
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class RuleRepositoryPlugin
{
    /**
     * Extension attribute factory
     *
     * @var ExtensionAttributesFactory
     */
    protected $extensionFactory;

    /**
     * Search criteria builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Gift rule repository
     *
     * @var GiftRuleRepositoryInterface
     */
    protected $giftRuleRepository;

    /**
     * Gift rule factory
     *
     * @var GiftRuleFactory
     */
    protected $giftRuleFactory;

    /**
     * RuleRepositoryPlugin constructor.
     *
     * @param ExtensionAttributesFactory  $extensionFactory      Extension factoryuuuuuuuuu
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder Search criteria builder
     * @param GiftRuleRepositoryInterface $giftRuleRepository    Gift rule repository
     * @param GiftRuleFactory             $giftRuleFactory       Gift rule factory
     */
    public function __construct(
        ExtensionAttributesFactory $extensionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GiftRuleRepositoryInterface $giftRuleRepository,
        GiftRuleFactory $giftRuleFactory
    ) {
        $this->extensionFactory      = $extensionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->giftRuleRepository    = $giftRuleRepository;
        $this->giftRuleFactory       = $giftRuleFactory;
    }

    /**
     * After save
     *
     * @param RuleRepositoryInterface $subject Subject
     * @param RuleInterface           $rule    Rule
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(RuleRepositoryInterface $subject, RuleInterface $rule)
    {
        $extensionAttributes = $rule->getExtensionAttributes();
        $giftRule = $extensionAttributes->getGiftRule();
        $this->giftRuleRepository->save($giftRule);

        return $rule;
    }

    /**
     * After get by id
     *
     * @param RuleRepositoryInterface $subject Subject
     * @param RuleInterface           $rule    Rule
     *
     * @return RuleInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(RuleRepositoryInterface $subject, RuleInterface $rule)
    {
        $extensionAttributes = $rule->getExtensionAttributes();

        if ($extensionAttributes === null) {
            /** @var RuleExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionFactory->create(RuleInterface::class);
            $rule->setExtensionAttributes($extensionAttributes);
        }

        try {
            /** @var GiftRuleInterface $giftRule */
            $giftRule = $this->giftRuleRepository->getById($rule->getRuleId());
        } catch (NoSuchEntityException $exception) {
            // Create gift rule if not exist.
            $giftRule = $this->giftRuleFactory->create();
            $giftRule->setId($rule->getRuleId());
        }
        $extensionAttributes->setGiftRule($giftRule);
        $rule->setExtensionAttributes($extensionAttributes);

        return $rule;
    }

    /**
     * After get list
     *
     * @param RuleRepositoryInterface $subject       Subject
     * @param SearchResults           $searchResults Search results
     *
     * @return SearchResults
     */
    public function afterGetList(RuleRepositoryInterface $subject, SearchResults $searchResults)
    {
        $newItem = [];
        /** @var RuleInterface $rule */
        foreach ($searchResults->getItems() as $rule) {
            $newItem[] = $this->afterGetById($subject, $rule);
        }

        return $searchResults->setItems($newItem);
    }
}
