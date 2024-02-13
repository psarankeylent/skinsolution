<?php

declare(strict_types=1);

namespace M1Subscription\M1SubscriptionGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class M1SubscriptionUpdateAddress implements ResolverInterface
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
        $this->validateInput($args);

        $reference_id   = $args['input']['reference_id'];
        $customer_address_id = $args['input']['customer_address_id'];
        $firstname      = $args['input']['firstname'];
        $lastname       = $args['input']['lastname'];
        $street         = $args['input']['street'];
        $city           = $args['input']['city'];
        $region         = $args['input']['region'];
        $region_id      = $args['input']['region_id'];
        $postcode       = $args['input']['postcode'];
        $telephone      = $args['input']['telephone'];

        try{
        $collection = $this->m1SubscriptionCollectionFactory->create()
            //->load(241214, "customer_id");
            ->getCollection()
            ->addFieldToFilter("reference_id", $reference_id);
            if($collection->count()>0){
                foreach ($collection as $value) {
                    $details = $value->getData('details');
                    $data = unserialize($details);
                    if(!empty($data))
                    {
                        $quoteId = $data['order_info']['entity_id'];
                    }
                }

                $quote = $this->quoteFactory->create()->load($quoteId);
                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setData('customer_address_id',$customer_address_id);
                $shippingAddress->setData('firstname',$firstname);
                $shippingAddress->setData('lastname',$lastname);
                $shippingAddress->setData('street',$street);
                $shippingAddress->setData('city',$city);
                $shippingAddress->setData('region',$region);
                $shippingAddress->setData('region_id',$region_id);
                $shippingAddress->setData('postcode',$postcode);
                $shippingAddress->setData('telephone',$telephone);
                $shippingAddress->setData('country_id','US');
                $shippingAddress->save();
                $response = "Data updated successfully";
            }else{
                $response = "Invalid reference id";
            }
        }catch(Exception $e){
            $response = $e->getMessage();

        }

        return ['CustomAlleObject' => array('response'=>$response)];

    }

    protected function validateInput($args)
    {
        $inputs = ['reference_id','customer_address_id','firstname','lastname','street','city','region','region_id','postcode','telephone'];

        if(!empty($inputs))
        {
            foreach ($inputs as $key => $value) {
                $inputValue = $args['input'][$value];
                if (!isset($inputValue) || empty($inputValue) ) {
                    throw new GraphQlInputException(__($inputs[$key]." value must be specified"));
                }
            }
            
        }
    }

}

