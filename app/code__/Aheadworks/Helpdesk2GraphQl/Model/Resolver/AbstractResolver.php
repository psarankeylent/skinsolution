<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver;

use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class AbstractResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Mutation
 */
abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        return $this->performResolve($field, $context, $info, $value, $args);
    }

    /**
     * Validate requested arguments
     *
     * @param array $args
     * @return bool
     */
    protected function validateArgs($args)
    {
        return true;
    }

    /**
     * Check if customer is authenticated
     *
     * @param ContextInterface $context
     * @return bool
     */
    protected function isCustomerAuthenticated($context)
    {
        return false !== $context->getExtensionAttributes()->getIsCustomer();
    }

    /**
     * Validate customer authentication
     *
     * @param ContextInterface $context
     * @throws GraphQlAuthorizationException
     */
    protected function validateCustomerAuthentication($context)
    {
        if (!$this->isCustomerAuthenticated($context)) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }
    }

    /**
     * Retrieve customer id from request context
     *
     * @param ContextInterface $context
     * @return int
     */
    protected function getCustomerId($context)
    {
        return $context->getUserId();
    }

    /**
     * Perform resolve method after validate customer authorization
     *
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws AggregateExceptionInterface
     * @throws ClientAware
     */
    abstract protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    );
}
