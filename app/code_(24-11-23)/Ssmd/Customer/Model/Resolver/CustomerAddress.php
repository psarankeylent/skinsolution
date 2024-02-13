<?php

declare(strict_types=1);

namespace Ssmd\Customer\Model\Resolver;

use Magento\Customer\Model\Customer;
use Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Newsletter\Model\Config;
use Magento\Sales\Model\Order\OrderCustomerExtractor;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Delegation\Storage;

use Magento\Sales\Model\Order\AddressRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;


/**
 * Create customer address resolver
 */
class CustomerAddress implements ResolverInterface
{
    /**
     * @var ExtractCustomerData
     */
    private $extractCustomerData;

    /**
     * @var CreateCustomerAccount
     */
    private $createCustomerAccount;

    /**
     * @var Config
     */
    private $newsLetterConfig;

    /**
     * @var OrderCustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * CreateCustomer constructor.
     *
     * @param ExtractCustomerData $extractCustomerData
     * @param CreateCustomerAccount $createCustomerAccount
     * @param Config $newsLetterConfig
     */
    public function __construct(
        ExtractCustomerData $extractCustomerData,
        CreateCustomerAccount $createCustomerAccount,
        Config $newsLetterConfig,
        OrderCustomerExtractor $customerExtractor,
        Storage $storage,
        AddressRepository $addressRepository,
        OrderRepositoryInterface $orderRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->newsLetterConfig = $newsLetterConfig;
        $this->extractCustomerData = $extractCustomerData;
        $this->createCustomerAccount = $createCustomerAccount;
        $this->customerExtractor = $customerExtractor;
        $this->storage = $storage;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        return $this->getSsmdAdditionalInfo($value['id']);
    }

    protected function getSsmdAdditionalInfo($addressId)
    {
        $this->searchCriteriaBuilder
            ->addFilter('customer_address_id', $addressId);

        $address = $this->addressRepository
            ->getList($this->searchCriteriaBuilder->create());

        $orders = [];

        foreach ($address->getData() as $a) {
            $orders[] = $a['parent_id'];
        }

        $sortOrder = $this->sortOrderBuilder->setField('created_at')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $this->searchCriteriaBuilder
            ->addFilter('entity_id', $orders, 'in')
            ->setSortOrders([$sortOrder]);

        $orders = $this->orderRepository
            ->getList($this->searchCriteriaBuilder->create());

        $lastUsed = ($orders->getFirstItem())? $orders->getFirstItem()['created_at'] : '';

        $orderIncrementIds = [];
        foreach ($orders as $order) {
            $orderIncrementIds[] = $order['increment_id'];
        }

        $this->searchCriteriaBuilder
            ->addFilter('increment_id', $orderIncrementIds, 'in');

        $subscriptionOrders = $this->subscriptionRepository
            ->getList($this->searchCriteriaBuilder->create());

        $activeSubscriptions = $subscriptionOrders->getTotalCount();

        return [
            'last_used' => $lastUsed,
            'active_subscriptions' => $activeSubscriptions
        ];
    }
}
