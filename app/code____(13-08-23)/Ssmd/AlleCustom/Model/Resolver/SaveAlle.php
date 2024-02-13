<?php

namespace Ssmd\AlleCustom\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Authorization\Model\UserContextInterface;


/**
 * Save Alle Class
 */
class SaveAlle implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $alleCustomFactory;
    protected $orderFactory;
   
    public function __construct(
        \Ssmd\AlleCustom\Model\AlleCustomFactory $alleCustomFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->alleCustomFactory = $alleCustomFactory;
        $this->orderFactory = $orderFactory;
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
        

        $this->validateInput($args);

        /** @var ContextInterface $context */
        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource'
                )
            );
        }
        try{

            // Save data
            $alleModel = $this->alleCustomFactory->create();
            
            $alleModel->setData('increment_id',$args['input']['increment_id'])
                      ->setData('customer_id',$args['input']['customer_id'])
                      ->setData('cart_id',$args['input']['cart_id'])
                      ->setData('quote_id',$args['input']['quote_id'])
                      ->setData('is_bdn',1)
                      ->setData('alle_email',$args['input']['alle_email'])
                      ->setData('alle_number',$args['input']['alle_number'])
                      ->setData('bdn',$args['input']['alle_email'])
                      ->setData('brilliantcoupon1',$args['input']['alle_points'])
                      ->setData('createdAt', date('Y-m-d h:i:s'))
                      ->save();

          
            
            return ['CustomAlleObject' => $alleModel->toArray()];

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
        }
    }

    /**
     * @param array $args
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    protected function validateInput($args)
    {

        $inputs = ['alle_email','alle_number','increment_id','cart_id','quote_id','customer_id','alle_points'];

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

