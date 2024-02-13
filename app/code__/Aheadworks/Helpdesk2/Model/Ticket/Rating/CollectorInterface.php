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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Interface CollectorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating
 */
interface CollectorInterface
{
    /**
     * Collect rating
     *
     * @param TicketInterface $ticket
     * @return float
     */
    public function collect($ticket);
}
