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
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail;

use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface CanSendEmailCheckerInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
interface CanSendEmailCheckerInterface
{
    /**
     * Can send email
     *
     * @param array $actionData
     * @param EventDataInterface $eventData
     * @return bool
     */
    public function canSend($actionData, $eventData);
}
