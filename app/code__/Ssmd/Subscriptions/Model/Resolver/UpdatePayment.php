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
 * Update Payment Data and BIlling address Class
 */
class UpdatePayment implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    
    protected $cardRepository;
    protected $cardFactory;
    protected $helper;
    protected $paymentFactory;
    protected $cartFactory;
    protected $paymentHelper;
    protected $addressHelper;

   
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Payment\Helper\Data $paymentHelper,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper
    ) {
        $this->graphQL = $graphQL;
        $this->cardRepository = $cardRepository;
        $this->cardFactory = $cardFactory;
        $this->helper = $helper;
        $this->paymentFactory = $paymentFactory;
        $this->cartFactory = $cartFactory;
        $this->paymentHelper = $paymentHelper;
        $this->addressHelper = $addressHelper;
    }

    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->graphQL->authenticate($context);
        
        try{
                $id            = $args['input']['hash'];
                $method        = $args['input']['method'];
                $customerId    = $args['input']['customer_id'];

                /*echo $args['input']['hash'];
                 $args['input']['payment']['cc_number'];
                exit;*/

                /** @var \ParadoxLabs\TokenBase\Model\Card $card */
                if (!empty($id)) {
                    $card = $this->cardRepository->getByHash($id);
                } else {
                    $card = $this->cardFactory->create();
                    $card->setMethod($card->getMethod() ?: $method);
                }
                
                $card = $card->getTypeInstance();
                //print_r($card->getData()); exit;

                $customer   = $this->helper->getCurrentCustomer();

                if ($card && (empty($id) || ($card->getHash() == $id && $card->hasOwner($customerId)))) {
                    /**
                     * Process address data
                     */
                    
                    // New billing input
                    $billingAddress  = $args['input']['billing_address'];
                    // New address
                    $newAddr = $this->addressHelper->buildAddressFromInput(
                        $billingAddress,
                        is_array($card->getAddress()) ? $card->getAddress() : [],
                        true
                    );

                    //echo "<pre>"; print_r($billingAddress); exit;
                    /**
                     * Process payment data
                     */
                    $cardData               = $args['input']['payment'];
                    $cardData['method']     = $method;
                    $cardData['card_id']    = $card->getId() > 0 ? $card->getHash() : '';

                    if (isset($cardData['cc_number'])) {
                        $cardData['cc_last4'] = substr($cardData['cc_number'], -4);
                        $cardData['cc_bin']   = substr($cardData['cc_number'], 0, 6);
                    }
                    //echo "<pre>"; print_r($cardData); exit;

                    /** @var \Magento\Quote\Model\Quote\Payment $newPayment */
                    $newPayment = $this->paymentFactory->create();
                    // Taken new quote object
                    $newPayment->setQuote($this->cartFactory->create()->getQuote());
                    //$newPayment->setQuote($this->checkoutSession->getQuote());
                    //$newPayment->getQuote()->getBillingAddress()->setCountryId($newAddr->getCountryId());
                    $newPayment->getQuote()->getBillingAddress()->setCountryId($billingAddress['country_id']);
                    $newPayment->importData($cardData);

                    $paymentMethod = $this->paymentHelper->getMethodInstance($card->getMethod());
                    $paymentMethod->setInfoInstance($newPayment);
                    $paymentMethod->validate();



                    /**
                     * Save payment data
                     */
                    $card->setMethod($method);
                    $card->setActive(1);
                    $card->setCustomer($customer);
                    $card->setAddress($newAddr);
                    $card->importPaymentInfo($newPayment);

                    $card = $this->cardRepository->save($card);
                    //print_r($card->getData()); exit;

                }
                else
                {
                    throw new \Exception('There is an error');
                }
               

                $data['cc_type'] = $card->getAdditional('cc_type');
                $data['cc_last4'] = $card->getAdditional('cc_last4');
                $data['cc_exp_year'] = $card->getAdditional('cc_exp_year');
                $data['cc_exp_month'] = $card->getAdditional('cc_exp_month');
                $data['payment_method'] = $card->getMethod();

                return ['PaymentData'=> $data];
       
        }catch(\Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
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
        $requiredFields = ['hash', 'customer_id', 'method'];
        foreach ($requiredFields as $v) {
            if (!isset($args['input'][ $v ]) || empty($args['input'][ $v ])) {
               /// echo $args[ $v ]; exit;
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }

        if(empty($args['input']['payment']['cc_type']))
        {
            throw new GraphQlInputException(__('cc_type value must be specified'));
        }
        else if(empty($args['input']['payment']['cc_number']))
        {
            throw new GraphQlInputException(__('cc_number value must be specified'));
        }
        else if(empty($args['input']['payment']['cc_exp_year']))
        {
            throw new GraphQlInputException(__('cc_exp_year value must be specified'));
        }
        else if(empty($args['input']['payment']['cc_exp_month']))
        {
            throw new GraphQlInputException(__('cc_exp_month value must be specified'));
        }
        else if(empty($args['input']['payment']['cc_cid']))
        {
            throw new GraphQlInputException(__('cc_cid value must be specified'));
        }

    }

}
