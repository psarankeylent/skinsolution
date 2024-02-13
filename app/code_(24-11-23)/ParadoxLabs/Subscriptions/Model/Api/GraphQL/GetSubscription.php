<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Model\Api\GraphQL;

/**
 * Soft dependency: Supporting 2.3 GraphQL without breaking <2.3 compatibility.
 * 2.3+ implements \Magento\Framework\GraphQL; lower does not.
 */
if (!interface_exists('\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface')) {
    if (interface_exists('\Magento\Framework\GraphQl\Query\ResolverInterface')) {
        class_alias(
            '\Magento\Framework\GraphQl\Query\ResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    } else {
        class_alias(
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\FauxResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    }
}

/**
 * Subscription Class
 */
class GetSubscription implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface
     */
    protected $customerSubscriptionRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * Subscription constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
    ) {
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->graphQL = $graphQL;
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
        $this->graphQL->authenticate($context);

        $subscriptions = $this->getSubscriptions(
            $context->getUserId(),
            isset($args['entity_id']) ? $args['entity_id'] : null
        );
        $requestedFields = $this->graphQL->getSubscriptionFields($info);

        /** @var \ParadoxLabs\Subscriptions\Model\Subscription[] $subscriptions */
        $output = [];
        foreach ($subscriptions as $subscription) {
            $output[] = $this->graphQL->convertSubscriptionForGraphQL($subscription, $requestedFields);
        }

        //$output['model'] = $output;
        //return ['model' => $output];
        return $output;
    }

    /**
     * Get subscription(s) for the given GraphQL request.
     *
     * @param int $customerId
     * @param int|null $id
     * @return \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getSubscriptions($customerId, $id = null)
    {
        $searchCriteria = $this->searchCriteriaBuilder;
        if ($id !== null) {
            $searchCriteria->addFilter('entity_id', $id);
        }

        $subscriptions  = $this->customerSubscriptionRepository->getList(
            $customerId,
            $searchCriteria->create()
        )->getItems();

        return $subscriptions;
    }
}
