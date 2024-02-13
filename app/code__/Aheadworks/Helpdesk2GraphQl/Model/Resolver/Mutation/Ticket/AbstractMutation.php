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
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Mutation\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class AbstractTicketMutation
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Mutation
 */
abstract class AbstractMutation implements ResolverInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    protected $ticketRepository;

    /**
     * @var ObjectConverter
     */
    protected $objectConverter;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        ObjectConverter $objectConverter
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['key'])) {
            throw new GraphQlInputException(__('Specify the "key" value.'));
        }

        return $this->performResolve($field, $context, $info, $value, $args);
    }

    /**
     * Get ticket by external link
     *
     * @param array $args
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    protected function getTicketByExternalLink($args)
    {
        $ticketLink = $args['key'];
        return $this->ticketRepository->getByExternalLink($ticketLink);
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
