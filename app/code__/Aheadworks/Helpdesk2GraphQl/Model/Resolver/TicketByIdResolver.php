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

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class TicketByIdResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver
 */
class TicketByIdResolver extends AbstractResolver
{
    /**
     * @var TicketRepositoryInterface
     */
    private $repository;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * @param TicketRepositoryInterface $repository
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        TicketRepositoryInterface $repository,
        ObjectConverter $objectConverter
    ) {
        $this->repository = $repository;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritDoc
     */
    protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validateCustomerAuthentication($context);
        $ticket = $this->repository->getById($args['entity_id']);
        $this->validateTicketOwnership($ticket, $context);

        return $this->objectConverter->convertToArray($ticket, TicketInterface::class);
    }

    /**
     * Validate ticket owner
     *
     * @param $ticket
     * @param $context
     * @throws GraphQlInputException
     */
    private function validateTicketOwnership($ticket, $context)
    {
        if ($this->getCustomerId($context) != $ticket->getCustomerId()) {
            throw new GraphQlInputException(__('This ticket does not belong to this customer.'));
        }
    }

    /**
     * @inheritDoc
     */
    protected function validateArgs($args)
    {
        if (isset($args['entity_id']) && $args['entity_id'] < 1) {
            throw new GraphQlInputException(__('Specify the "entity_id" value.'));
        }
    }
}
