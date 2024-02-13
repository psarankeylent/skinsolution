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
 * Change Frequency Class
 */
class ChangeFrequency implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
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
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource,
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager = null
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->config = $config;
        $this->statusSource = $statusSource;
        $this->itemManager = $itemManager ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Service\ItemManager::class
        );
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

        try{

            $subscription = $this->subscriptionRepository->load($args['id']);

            //  =============== Changed code(5-06-23) Start ===================
            
            if (!empty($args['interval']) && $args['interval'] !== '0') {
                $this->updateInterval($subscription, (int)$args['interval']);
            }

            //  =============== Changed code(5-06-23) End ===================

            return ['SubscriptionObj' => $subscription->toArray()];

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
        }
    }


    /**
     * Update subscription frequency/pricing based on the given interval, if valid and changed
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param int $optionValueId
     * @return void
     */
    protected function updateInterval(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        int $optionValueId
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();
        $item  = current($quote->getAllVisibleItems());

        $this->itemManager->updateInterval($subscription, $item, $optionValueId);
       
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote        = $subscription->getQuote();

        $subscription->addRelatedObject($quote, true);
        $this->subscriptionRepository->save($subscription);
    }


}
