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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as TicketPriority;

/**
 * Class General
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend
 */
class General implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $data[TicketInterface::STATUS_ID] = TicketStatus::NEW;
        $data[TicketInterface::PRIORITY_ID] = TicketPriority::TO_DO;

        return $data;
    }
}
