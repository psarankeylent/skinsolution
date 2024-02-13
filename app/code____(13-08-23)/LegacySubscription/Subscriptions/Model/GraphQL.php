<?php

namespace LegacySubscription\Subscriptions\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * GraphQL Class
 */
class GraphQL
{
    /**
     * Verify the requester is authorized to request data.
     *
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException
     */
    public function authenticate(
        \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
    ) {
        
        /** @var ContextInterface $context */
        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource "%1"',
                    'legacy subscriptions'
                )
            );
        }
    }


}
