<?php

declare(strict_types=1);

namespace M1Subscription\M1SubscriptionGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Authorization\Model\UserContextInterface;


class M1SubscriptionDetails implements ResolverInterface
{
    protected $quoteFactory;
    protected $m1SubscriptionCollectionFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollectionFactory  $m1SubscriptionCollectionFactory,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->m1SubscriptionCollectionFactory = $m1SubscriptionCollectionFactory;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;

    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */

        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource'
                )
            );
        }

        $reference_id = $args['reference_id'];

        $legacySubscription = $this->customerSubscriptionFactory->create()->getCollection();
        $legacySubscription->addFieldToFilter('profile_id', $reference_id);

        foreach ($legacySubscription AS $legacySubscriptionItems) {
            $shipping['firstname']  = $legacySubscriptionItems['ship_first_name'];
            $shipping['lastname']   = $legacySubscriptionItems['ship_last_name'];
            $shipping['street']     = $legacySubscriptionItems['ship_address'];
            $shipping['city']       = $legacySubscriptionItems['ship_city'];
            $shipping['region']     = $legacySubscriptionItems['ship_state'];
            $shipping['postcode']   = $legacySubscriptionItems['ship_zip'];
            $shipping['telephone']  = $legacySubscriptionItems['ship_telephone'];
            $shipping['customer_address_id'] = '';
            $shipping['address_id'] = 123;
            $shipping['region_id']  = 11;

            $billing['firstname']  = $legacySubscriptionItems['bill_first_name'];
            $billing['lastname']   = $legacySubscriptionItems['bill_last_name'];
            $billing['street']     = $legacySubscriptionItems['bill_address'];
            $billing['city']       = $legacySubscriptionItems['bill_city'];
            $billing['region']     = $legacySubscriptionItems['bill_state'];
            $billing['postcode']   = $legacySubscriptionItems['bill_zip'];
            $billing['telephone']  = $legacySubscriptionItems['bill_telephone'];
            $billing['customer_address_id'] = '';
            $billing['address_id'] = 123;
            $billing['region_id']  = 11;

            $payments['payment_id']  = 123;
            $payments['quote_id']  = 123;
            $payments['method']  = '';
            $payments['cc_type']  = '';
            $payments['cc_last_4']  = '';
            $payments['cc_exp_month']  = '';
            $payments['cc_exp_year']  = '';
        }

        return array("shipping_address"=>$shipping,"billing_address"=>$billing,"payments"=>$payments);

    }

}


