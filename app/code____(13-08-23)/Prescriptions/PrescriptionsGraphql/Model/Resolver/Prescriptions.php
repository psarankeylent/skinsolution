<?php
declare(strict_types=1);

namespace Prescriptions\PrescriptionsGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Prescriptions implements ResolverInterface
{

    protected $prescriptionsFactory;

    public function __construct(
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory
    ) {
        $this->prescriptionsFactory = $prescriptionsFactory;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $prescriptions = $this->prescriptionsFactory->create()->getCollection();
        $prescriptionsItems = $prescriptions->getItems();
        return $prescriptionsItems;
    }

}

