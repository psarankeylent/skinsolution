<?php
namespace Ssmd\ParadoxLabs\Model\Api\GraphQL;

/**
 * Card Class
 */
class GetCards extends \ParadoxLabs\TokenBase\Model\Api\GraphQL\GetCards
{
    /**
     * @var \ParadoxLabs\TokenBase\Api\CustomerCardRepositoryInterface
     */
    private $customerCardRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \ParadoxLabs\TokenBase\Model\Api\GraphQL
     */
    private $graphQL;

    private $customerRepository;

    /**
     * Card constructor.
     *
     * @param \ParadoxLabs\TokenBase\Api\CustomerCardRepositoryInterface $customerCardRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Api\CustomerCardRepositoryInterface $customerCardRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Ssmd\ParadoxLabs\Model\Api\GraphQL $graphQL,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerCardRepository = $customerCardRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->graphQL                = $graphQL;
        $this->customerRepository     = $customerRepository;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->graphQL->authenticate($context, true);

        /** @var \ParadoxLabs\TokenBase\Model\Card[] $cards */
        $cards  = $this->getCards(
            $context->getUserId(),
            isset($args['hash']) ? $args['hash'] : null
        );

        $defaultPaymentId = $this->getCustomerDefaultPaymentId($context->getUserId());

        $output = [];
        foreach ($cards as $card) {
            $output[] = $this->graphQL->convertCardForOutput($card, $defaultPaymentId);
        }

        return $output;
    }

    /**
     * @param int $customerId
     * @param string|null $hash
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCards($customerId, $hash)
    {
        $searchCriteria = $this->searchCriteriaBuilder;

        // Filter results by a specific hash if given
        if (!empty($hash)) {
            $searchCriteria->addFilter('hash', $hash);
        }

        $searchCriteria = $searchCriteria->create();

        return $this->customerCardRepository->getList($customerId, $searchCriteria)->getItems();
    }

    public function getCustomerDefaultPaymentId($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);

        return $customer->getCustomAttribute('default_payment')->getValue();
    }
}
