<?php
declare(strict_types=1);

namespace Ssmd\StoreCredit\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Storecredit implements ResolverInterface
{

    protected $storecreditFactory;

    public function __construct(
        \Ssmd\StoreCredit\Model\StorecreditFactory  $storecreditFactory
    ) {
        $this->storecreditFactory = $storecreditFactory;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        //return $storecredits = $this->storecreditFactory->create()->getCollection();
        //$storecreditsItems = $storecredits->getItems();
        //return $storecreditsItems;
        return "chk by skp";
    }

}

