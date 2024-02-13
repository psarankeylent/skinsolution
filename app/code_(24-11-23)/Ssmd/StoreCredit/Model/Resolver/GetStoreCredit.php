<?php
declare(strict_types=1);

namespace Ssmd\StoreCredit\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetStoreCredit implements ResolverInterface
{

    protected $storecreditFactory;

    public function __construct(
        \Ssmd\StoreCredit\Model\StorecreditFactory  $storecreditFactory
    ) {
        $this->storecreditFactory = $storecreditFactory;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customerId = $context->getUserId();

        $collection = $this->storecreditFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customerId)
                        ->setOrder('id','DESC');

        $amounts = null;
        if($collection->count() > 0) {
            $amounts = $collection->getFirstItem()->getData()['amount'];
        }
        return array('amount' => $amounts);
    }
}

