<?php

declare(strict_types=1);

namespace Ssmd\Faqs\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Ssmd\Faqs\Model\ResourceModel\Faq\CollectionFactory;

class Faqs implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $faqCollectionFactory;

    public function __construct(\Ssmd\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory) {
        $this->faqCollectionFactory = $faqCollectionFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try{
            $faqs = $this->faqCollectionFactory->create();

            $items = $faqs->getItems();

            return $items;

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        } catch (LocalizedException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }
        return [];
    }
}
