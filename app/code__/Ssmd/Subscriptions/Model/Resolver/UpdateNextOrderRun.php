<?php

namespace Ssmd\Subscriptions\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

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
 *  Update NextOrderRun Class
 */
class UpdateNextOrderRun implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface
     */
    protected $customerSubscriptionRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;
    protected $subscriptionRepository;

    /**
     * ChangeStatus constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->config = $config;
        $this->statusSource = $statusSource;
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

        //$this->validateInput($args);

        try{

            $subscription = $this->subscriptionRepository->load($args['id']);

            $subscription->setData('next_run', $args['next_run']);
            $subscriptionModel = $this->subscriptionRepository->save($subscription);
       
            return ['SubscriptionObj' => $subscription->toArray()];
       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
        }

    }

    /**
     * Validate GraphQL request input.
     *
     * @param array $args
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    protected function validateInput(array $args)
    {
        $requiredFields = ['id', 'next_run'];
        foreach ($requiredFields as $v) {
            if (!isset($args[ $v ]) || empty($args[ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}
