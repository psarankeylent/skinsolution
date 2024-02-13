<?php

declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ConsultOnly implements ResolverInterface
{
    private $consultOnlyDataProvider;

    /**
     * @param DataProvider\ConsultOnly $consultOnlyRepository
     */
    public function __construct(
        \ConsultOnly\ConsultOnlyGraphql\Model\GraphQL $graphQL,
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory  $consultOnlyFactory
    ) {
        $this->graphQL = $graphQL;
        $this->consultOnlyFactory = $consultOnlyFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null) {
        $this->graphQL->authenticate($context);
        $consultOnly = $this->consultOnlyFactory->create()
                        ->getCollection()
                        ->addFieldToFilter("customer_id", $context->getUserId());

        return $consultOnly;
    }

}


