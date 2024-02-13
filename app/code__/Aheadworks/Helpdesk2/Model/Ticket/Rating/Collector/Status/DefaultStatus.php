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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class DefaultStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status
 */
class DefaultStatus extends AbstractCollector
{
    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        return 0;
    }

    /**
     * Is need to apply points to ticket
     *
     * @return bool
     */
    protected function isNeedToApplyPointsToTicket()
    {
        return false;
    }
}
