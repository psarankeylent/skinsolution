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
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Create
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Mutation\Ticket
 */
class Create implements ResolverInterface
{
    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * @param ObjectConverter $objectConverter
     * @param CommandInterface $createCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        ObjectConverter $objectConverter,
        CommandInterface $createCommand,
        ProcessorInterface $postDataProcessor
    ) {
        $this->createCommand = $createCommand;
        $this->postDataProcessor = $postDataProcessor;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $data = $this->prepareOptions($args);
        if ($this->isCustomerAuthenticated($context)) {
            $data[TicketInterface::CUSTOMER_ID] = $this->getCustomerId($context);
        }

        $ticketData = $this->postDataProcessor->prepareEntityData($data);
        $ticket = $this->createCommand->execute($ticketData);

        return $this->objectConverter->convertToArray($ticket, TicketInterface::class);
    }

    /**
     * Prepare department options
     *
     * @param array $args
     * @return array
     */
    private function prepareOptions($args)
    {
        $args[TicketInterface::OPTIONS] = array_reduce(
            $args[TicketInterface::OPTIONS] ?? [],
            function ($carry, $item) {
                $carry[$item['department_option_id']] = $item['value'];
                return $carry;
            },
            []
        );

        return $args;
    }

    /**
     * Check if customer is authenticated
     *
     * @param ContextInterface $context
     * @return bool
     */
    private function isCustomerAuthenticated($context)
    {
        return false !== $context->getExtensionAttributes()->getIsCustomer();
    }


    /**
     * Retrieve customer id from request context
     *
     * @param ContextInterface $context
     * @return int
     */
    private function getCustomerId($context)
    {
        return $context->getUserId();
    }
}
