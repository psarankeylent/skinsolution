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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Api\TicketPriorityRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class TicketStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class TicketPriority implements ModifierInterface
{
    /**
     * @var TicketPriorityRepositoryInterface
     */
    private $priorityRepository;

    /**
     * @param TicketPriorityRepositoryInterface $priorityRepository
     */
    public function __construct(
        TicketPriorityRepositoryInterface $priorityRepository
    ) {
        $this->priorityRepository = $priorityRepository;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $priority = $this->priorityRepository->get($eventData->getTicket()->getPriorityId());
        $templateVariables[EmailVariables::TICKET_PRIORITY] = __($priority->getLabel())->render();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
