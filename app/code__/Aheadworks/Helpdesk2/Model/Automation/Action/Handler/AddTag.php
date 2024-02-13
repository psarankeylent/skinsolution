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

/**
 * Class AddTag
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class AddTag implements ActionInterface
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
     * @param TicketRepositoryInterface $ticketRepository
     * @param MessageManagement $messageManagement
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        MessageManagement $messageManagement
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->messageManagement = $messageManagement;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $ticket = $eventData->getTicket();
        $prevValue = is_array($ticket->getTagNames()) ? $ticket->getTagNames() : [];
        $newValue = array_merge($prevValue, $actionData['value']);
        $newValue = array_unique($newValue);

        if ($this->checkChanges($prevValue, $newValue)) {
            $ticket->setTagNames($newValue);
            $this->ticketRepository->save($ticket);

            $this->messageManagement->createAutomationMessage(
                $ticket->getEntityId(),
                $this->getValue($prevValue),
                $this->getValue($newValue),
                $eventData->getEventName()
            );
        }

        return true;
    }

    /**
     * Prepare value
     *
     * @param array $valueArray
     * @return string
     */
    private function getValue($valueArray)
    {
        return implode(', ', $valueArray);
    }

    /**
     * Check if tag names was changed
     *
     * @param array $prevValue
     * @param array $newValue
     * @return bool
     */
    private function checkChanges($prevValue, $newValue)
    {
        return array_udiff($prevValue, $newValue, 'strcasecmp')
            != array_udiff($newValue, $prevValue, 'strcasecmp');
    }
}
