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
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as Source;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ChangePriority
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class ChangePriority implements ActionInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var MessageManagement
     */
    private $messageManagement;

    /**
     * @var Source
     */
    private $source;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param MessageManagement $messageManagement
     * @param Source $source
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        MessageManagement $messageManagement,
        Source $source
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->messageManagement = $messageManagement;
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $ticket = $eventData->getTicket();
        $prevValue = $ticket->getPriorityId();
        $newValue = $actionData['value'];

        if ($ticket->getPriorityId() == $newValue) {
            return false;
        }
        $ticket->setPriorityId($newValue);
        $this->ticketRepository->save($ticket);

        $this->messageManagement->createAutomationMessage(
            $ticket->getEntityId(),
            $this->getOptionLabel($prevValue),
            $this->getOptionLabel($newValue),
            $eventData->getEventName()
        );

        return true;
    }

    /**
     * Retrieve option Label by id
     *
     * @param $id
     * @return string
     */
    private function getOptionLabel($id) {
        try {
            return $this->source->getOptionById($id)['label'];
        } catch (LocalizedException $exception) {
            return '';
        }
    }
}
