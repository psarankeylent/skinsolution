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
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread\DiscussionMessages as DiscussionMessagesProvider;
use Aheadworks\Helpdesk2GraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class MessagesResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket
 */
class MessagesResolver extends AbstractResolver
{
    /**
     * @var DiscussionMessagesProvider
     */
    private $messagesProvider;

    /**
     * @param DiscussionMessagesProvider $messagesProvider
     */
    public function __construct(
        DiscussionMessagesProvider $messagesProvider
    ) {
        $this->messagesProvider = $messagesProvider;
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
        /** @var TicketInterface $ticket */
        $ticket = $value['model'];

        return $this->messagesProvider->getData($ticket->getEntityId())['items'];
    }
}
