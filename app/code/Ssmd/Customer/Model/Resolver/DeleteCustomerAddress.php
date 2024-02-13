<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\Customer\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\Address\DeleteCustomerAddress as DeleteCustomerAddressModel;
use Magento\CustomerGraphQl\Model\Customer\Address\GetCustomerAddress;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Model\QuoteFactory;
use ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory as SubscriptionCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory as AddressCollectionFactory;

/**
 * Customers address delete, used for GraphQL request processing.
 */
class DeleteCustomerAddress implements ResolverInterface
{
    /**
     * @var GetCustomerAddress
     */
    private $getCustomerAddress;

    /**
     * @var DeleteCustomerAddressModel
     */
    private $deleteCustomerAddress;

    /**
     * @var
     */
    protected $quoteFactory;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var
     */
    protected $addressCollectionFactory;

    /**
     * @param GetCustomerAddress $getCustomerAddress
     * @param DeleteCustomerAddressModel $deleteCustomerAddress
     */
    public function __construct(
        GetCustomerAddress $getCustomerAddress,
        DeleteCustomerAddressModel $deleteCustomerAddress,
        QuoteFactory $quoteFactory,
        SubscriptionCollectionFactory $subscriptionColnFactory,
        AddressCollectionFactory $addressCollectionFactory
    ) {
        $this->getCustomerAddress = $getCustomerAddress;
        $this->deleteCustomerAddress = $deleteCustomerAddress;
        $this->quoteFactory = $quoteFactory;
        $this->subscriptionCollectionFactory = $subscriptionColnFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
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
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $address = $this->getCustomerAddress->execute((int)$args['id'], $context->getUserId());

        if($this->hasSubscriptionsWithAddress($address)) {
            throw new GraphQlInputException(
                __('The address cannot be deleted as it is associated with an active Subscription.', [$address->getId()])
            );
        }

        $this->deleteCustomerAddress->execute($address);
        return true;
    }

    public function hasSubscriptionsWithAddress($address)
    {
        $quoteIds = [];

        $addressCollection = $this->addressCollectionFactory->create();
        $addressCollection->addFieldToFilter('customer_address_id', $address->getId());
        $addressCollection->addFieldToSelect('quote_id');

        foreach ($addressCollection as $address) {
            $quoteIds[] = $address->getQuoteId();
        }

        $subscriptions = $this->subscriptionCollectionFactory->create();
        $subscriptions->addFieldToSelect('increment_id');
        $subscriptions->addFieldToFilter('status', ['in' => ['active', 'paused']]);
        $subscriptions->addFieldToFilter('quote_id', ['in' => $quoteIds]);

        $subscriptionIds = $subscriptions->getColumnValues('increment_id');

        return !empty($subscriptionIds);
    }
}
