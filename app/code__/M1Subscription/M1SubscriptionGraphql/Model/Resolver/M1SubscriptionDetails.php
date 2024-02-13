<?php

declare(strict_types=1);

namespace M1Subscription\M1SubscriptionGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class M1SubscriptionDetails implements ResolverInterface
{
    protected $quoteFactory;
    protected $m1SubscriptionCollectionFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollectionFactory  $m1SubscriptionCollectionFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->m1SubscriptionCollectionFactory = $m1SubscriptionCollectionFactory;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        /*
        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource'
                )
            );
        }    
        */

        $reference_id = $args['reference_id'];

        $collection = $this->m1SubscriptionCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter("reference_id", $reference_id);

        if($collection->count()>0){
            foreach ($collection as $value) {
                $details    = $value->getData('details');
                $data       = unserialize($details);
                if(!empty($data))
                {
                    $quoteId = $data['order_info']['entity_id'];
                }
            }
        }

        if($quoteId){
            $quote = $this->quoteFactory->create()->load($quoteId);
            $shippingAddress    = $quote->getShippingAddress();
            $billingAddress     = $quote->getBillingAddress();
            $payments           = $quote->getPayment();
        }

        return array("shipping_address"=>$shippingAddress,"billing_address"=>$billingAddress,"payments"=>$payments);

    }

}

