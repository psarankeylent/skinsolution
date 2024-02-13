<?php

namespace Backend\PauseSubscription\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Model\QuoteFactory;

class pauseSubscription implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $graphQL;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory  $subscriptionFactory,
        \Backend\PauseSubscription\Model\PauseSubscriptionFactory  $pauseSubscriptionFactory,
        QuoteFactory $quoteFactory
    ) {
        $this->quoteRepository      = $quoteRepository;
        $this->quoteFactory         = $quoteFactory;
        $this->subscriptionFactory  = $subscriptionFactory;
        $this->pauseSubscriptionFactory = $pauseSubscriptionFactory;
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
        //$this->graphQL->authenticate($context);
        //$context->getUserId();
        
        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource'
                )
            );
        }

        try{
            
            //isset($args['entity_id']) ? $args['entity_id'] : null

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pause_subscription.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $subscription_id    = isset($args['input']['subscription_id']) ? $args['input']['subscription_id'] : null;
            $pause_reason       = isset($args['input']['pause_reason']) ? $args['input']['pause_reason'] : null;
            $discount_accepted  = isset($args['input']['discount_accepted_by_customer']) ? $args['input']['discount_accepted_by_customer'] : null;

            if($discount_accepted == strtolower(trim('yes'))){

                $recordExist = $this->pauseSubscriptionFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("subscription_id", $subscription_id)
                    ->addFieldToFilter("discount_accepted_by_customer", 'yes')
                    ->getFirstItem();
            
                if(!$recordExist->getData('id')){

                    $discountAcceptedPauseSubscription = $this->pauseSubscriptionFactory->create();
                    $discountAcceptedPauseSubscription->setData('customer_id',$context->getUserId())
                        ->setData('subscription_id',$subscription_id)
                        ->setData('pause_reason',$pause_reason)
                        ->setData('discount_accepted_by_customer','yes');
                    $discountAcceptedPauseSubscription->save();

                    // updating discountedTotal //
                    $subscriptionFactory = $this->subscriptionFactory->create()
                        ->getCollection()
                        ->addFieldToFilter("entity_id", $subscription_id)
                        ->getFirstItem();

                    $quote_id = $subscriptionFactory->getData('quote_id');

                    $quote = $this->quoteFactory->create()->load($quote_id);
                    $items = $quote->getAllItems();
                      foreach ($items as $quoteItem){
                        $originalCustomPrice = $quoteItem->getData('original_custom_price');
                        $discount = 10; // (Its 10 percentage)

                        $discountedTotal = $originalCustomPrice - ($originalCustomPrice * ($discount/100));
                        $discountedTotal = number_format((float)$discountedTotal, 2, '.', '');
                        $quoteItem->setCustomPrice($discountedTotal);
                        $quoteItem->setOriginalCustomPrice($discountedTotal);
                        $quoteItem->save();
                    }

                    $grandTotal = $quote->getGrandTotal();
                    $grandTotal = $grandTotal - ($grandTotal * ($discount/100));
                    $grandTotal = number_format((float)$grandTotal, 2, '.', '');

                    $quote->setGrandTotal($grandTotal);
                    $this->quoteRepository->get($quote_id);
                    $this->quoteRepository->save($quote->collectTotals());

                    $existingValue = $subscriptionFactory->getData('additional_information');
                    if(!empty($existingValue)){
                        $existingValue = json_decode($existingValue, true); 
         
                        foreach ($existingValue as $key => $value) {
                            $newAdditionalInfo = array("availed_special_offer"=>true, $key =>$value);
                        }
                    }else{
                        $newAdditionalInfo = array("availed_special_offer"=>true);
                    }

                    $newAdditionalInfo = json_encode($newAdditionalInfo);
                    $updateDiscount = array('subtotal' => $discountedTotal,'additional_information' => $newAdditionalInfo);
                    $subscriptionFactory->addData($updateDiscount);
                    $subscriptionFactory->save();

                $message = "Successfully availed special offer for subscription id ".$subscription_id;
                } // if recordExist 
                else if($recordExist->getData('id')){
                    $message = "Already availed special offer for subscription id ".$subscription_id;
                }

            }
            else if($discount_accepted == strtolower(trim('no'))){

                $pauseSubscriptionFactoryInsert = $this->pauseSubscriptionFactory->create();
                $pauseSubscriptionFactoryInsert->setData('customer_id',$context->getUserId())
                    ->setData('subscription_id',$subscription_id)
                    ->setData('pause_reason',$pause_reason)
                    ->setData('discount_accepted_by_customer','no');
                $pauseSubscriptionFactoryInsert->save();
                $message = "Added successfully for subscription id ".$subscription_id;
            }


        }catch(Exception $e){
            $message = "something went wrong";
        }

        $output = array('message' => $message);
        return $output;

    }


}


