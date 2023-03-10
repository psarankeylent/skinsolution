<?php

namespace Ssmd\TokenBase\Model\Api\GraphQL;

/**
 * Card Class
 */
class GetCards extends \ParadoxLabs\TokenBase\Model\Api\GraphQL\GetCards
{
    public $orderFactory;
    public $graphQL;

    public function __construct(
        \ParadoxLabs\TokenBase\Api\CustomerCardRepositoryInterface $customerCardRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->customerCardRepository = $customerCardRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->graphQL                = $graphQL;
        $this->orderFactory           = $orderFactory;
        parent::__construct($customerCardRepository, $searchCriteriaBuilder, $graphQL);
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
        $this->graphQL->authenticate($context, true);

        /** @var \ParadoxLabs\TokenBase\Model\Card[] $cards */
        $cards  = $this->getCards(
            $context->getUserId(),
            isset($args['hash']) ? $args['hash'] : null
        );
        $output = [];
        foreach ($cards as $card) {
            $output[] = $this->graphQL->convertCardForOutput($card);
        }

        return $output;
    }

    /**
     * @param int $customerId
     * @param string|null $hash
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCards($customerId, $hash)
    {
        
        $searchCriteria = $this->searchCriteriaBuilder;

        // Filter results by a specific hash if given
        if (!empty($hash)) {
            $searchCriteria->addFilter('hash', $hash);
        }

        $searchCriteria = $searchCriteria->create();        
        $items = $this->customerCardRepository->getList($customerId, $searchCriteria)->getItems();

       // Customer cards
        // @return an array of tokenbase_ids
        $tokenBaseIdsArray = $this->checkCustomerPendingOrders($customerId);
        //echo "<pre>"; print_r($tokenBaseIdsArray); exit;

       foreach ($items as $item) {
            //$method = $item->getData('method');
            $tokenbaseId = $item->getData('id');            
            if(in_array($tokenbaseId, $tokenBaseIdsArray))
            {
                $item->setData('is_in_use', true);
            }
            else
            {
                $item->setData('is_in_use', false);
            }
        }
        
        return $items;
        //return $this->customerCardRepository->getList($customerId, $searchCriteria)->getItems();
    }

    //@return tokenBase_id array function by customerid
    public function checkCustomerPendingOrders($customerId)
    {
        $collection = $this->orderFactory->create()->getCollection();       

        //$orders = $collection->create();
        $collection->addAttributeToSelect('*')
               ->addAttributeToFilter('customer_id', $customerId)
               ->addAttributeToFilter('status', ['like' => 'pending%']);

        $tokenBaseIdsArray = [];
        if($collection->getSize()>0)
        {
            foreach ($collection->getData() as $ord) {
                
                $order = $this->orderFactory->create()->load($ord['entity_id']);

                $payment = $order->getPayment();
                $tokenBaseIdsArray[] = $payment->getData('tokenbase_id');

            }
        }
        return $tokenBaseIdsArray;
    }
}
