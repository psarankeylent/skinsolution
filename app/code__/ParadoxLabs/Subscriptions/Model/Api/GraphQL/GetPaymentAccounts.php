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
 * GetPaymentAccounts Class
 */
class GetPaymentAccounts implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     */
    protected $paymentService;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface
     */
    protected $customerSubscriptionRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Vault
     */
    protected $vaultHelper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Subscription constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService,
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->graphQL = $graphQL;
        $this->paymentService = $paymentService;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->vaultHelper = $vaultHelper;
        $this->customerRepository = $customerRepository;
        $this->registry = $registry;
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

        if (!empty($args['subscription_id'])) {
            $customer = $this->customerRepository->getById($context->getUserId());
            $this->registry->register('current_customer', $customer);

            $subscription = $this->customerSubscriptionRepository->getById(
                $context->getUserId(),
                $args['subscription_id']
            );

            $cards = $this->paymentService->getActiveCustomerCardsForQuote(
                $subscription->getQuote()
            );
        } else {
            $cards = $this->paymentService->getActiveCustomerCards($context->getUserId());
        }

        $output = [];
        foreach ($cards as $card) {
            $output[] = [
                'public_hash' => $card->getPublicHash(),
                'label' => $this->vaultHelper->getCardLabel($card),
                'method' => $card->getPaymentMethodCode(),
                'cc_type' => $this->vaultHelper->getCardType($card),
                'cc_exp' => $this->vaultHelper->getCardExpires($card),
                'cc_last4' => $this->vaultHelper->getCardLast4($card),
            ];
        }

        return $output;
    }
}
