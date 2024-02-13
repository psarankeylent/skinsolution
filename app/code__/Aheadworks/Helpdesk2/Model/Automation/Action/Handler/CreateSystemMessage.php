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
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;

/**
 * Class CreateSystemMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class CreateSystemMessage implements ActionInterface
{
    /**
     * @var MessageManagement
     */
    private $messageManagement;
    
    /**
     * @param MessageManagement $messageManagement
     */
    public function __construct(
        MessageManagement $messageManagement
    ) {
        $this->messageManagement = $messageManagement;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $this->messageManagement->createAutomationMessage(
            $eventData->getTicket()->getEntityId(),
            '',
            $actionData['value'],
            $eventData->getEventName()
        );

        return true;
    }
}
