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
namespace Aheadworks\Helpdesk2\Model\Automation;

/**
 * Interface TaskInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
interface TaskInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const AUTOMATION_ID = 'automation_id';
    const TICKET_ID = 'ticket_id';
    const ACTION_TYPE = 'action_type';
    const ACTION_DATA = 'action_data';
    const STATUS = 'status';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get automation ID
     *
     * @return int
     */
    public function getAutomationId();

    /**
     * Set automation ID
     *
     * @param int $automationId
     * @return $this
     */
    public function setAutomationId($automationId);

    /**
     * Get ticket ID
     *
     * @return int
     */
    public function getTicketId();

    /**
     * Set ticket ID
     *
     * @param int $ticketId
     * @return $this
     */
    public function setTicketId($ticketId);

    /**
     * Get action type
     *
     * @return string
     */
    public function getActionType();

    /**
     * Set action type
     *
     * @param string $actionType
     * @return $this
     */
    public function setActionType($actionType);

    /**
     * Get action data
     *
     * @return string|array
     */
    public function getActionData();

    /**
     * Set action data
     *
     * @param string $actionData
     * @return $this
     */
    public function setActionData($actionData);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);
}
