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

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Reply
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Mutation\Ticket
 */
class Reply extends AbstractMutation
{
    /**
     * @var CommandInterface
     */
    private $replyCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param ObjectConverter $objectConverter
     * @param CommandInterface $replyCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        ObjectConverter $objectConverter,
        CommandInterface $replyCommand,
        ProcessorInterface $postDataProcessor
    ) {
        parent::__construct($ticketRepository, $objectConverter);
        $this->replyCommand = $replyCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $ticket = $this->getTicketByExternalLink($args);

        $data = [
            MessageInterface::CONTENT => $args[MessageInterface::CONTENT],
            MessageInterface::ATTACHMENTS => $args[MessageInterface::ATTACHMENTS] ?? [],
            TicketInterface::ENTITY_ID => $ticket->getEntityId(),
            TicketInterface::CUSTOMER_NAME => $ticket->getCustomerName(),
            TicketInterface::CUSTOMER_EMAIL => $ticket->getCustomerEmail()
        ];
        $ticketData = $this->postDataProcessor->prepareEntityData($data);
        $ticket = $this->replyCommand->execute($ticketData);

        return $this->objectConverter->convertToArray($ticket, TicketInterface::class);
    }
}
