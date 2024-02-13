<?php

declare(strict_types=1);

namespace M1Subscription\M1SubscriptionGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Model\QuoteFactory;

class M1SubscriptionDetails implements ResolverInterface
{
    protected $quoteFactory;
    protected $m1SubscriptionCollectionFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollectionFactory $m1SubscriptionCollectionFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->m1SubscriptionCollectionFactory = $m1SubscriptionCollectionFactory;
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

        $customer_id    = $context->getUserId();
        $reference_id   = $args['reference_id'];

        $shippingAddress    = []; 
        $billingAddress     = []; 
        $payments           = [];

        $collection = $this->m1SubscriptionCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter("reference_id", $reference_id)
            ->addFieldToFilter("customer_id", $customer_id);

        if($collection->count()>0){
            foreach ($collection as $value) {
                $details    = $value->getData('details');
                $data       = unserialize($details);
                if(!empty($data))
                {
                    $quoteId = $data['order_info']['entity_id'];
                }
            }

            $shippingAddress    = $data['shipping_address'];
            $billingAddress     = $data['billing_address'];

        }

        return array("shipping_address"=>$shippingAddress,"billing_address"=>$billingAddress,"payments"=>$payments);

        /*
        if($quoteId){
            $quote = $this->quoteFactory->create()->load($quoteId);
            $shippingAddress    = $quote->getShippingAddress();
            $billingAddress     = $quote->getBillingAddress();
            $payments           = $quote->getPayment();
        }
        return array("shipping_address"=>$shippingAddress,"billing_address"=>$billingAddress,"payments"=>$payments);
        */

    }

}


