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
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AutomationInterface
 * @api
 */
interface AutomationInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const IS_ACTIVE = 'is_active';
    const PRIORITY = 'priority';
    const IS_REQUIRED_TO_BREAK = 'is_required_to_break';
    const EVENT = 'event';
    const CONDITIONS = 'conditions';
    const ACTIONS = 'actions';
    /**#@-*/

    /**
     * Get automation ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set automation ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get automation name
     *
     * @return string
     */
    public function getName();

    /**
     * Set automation name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get is required to break
     *
     * @return bool
     */
    public function getIsRequiredToBreak();

    /**
     * Set is required to break
     *
     * @param bool $isRequiredToBreak
     * @return $this
     */
    public function setIsRequiredToBreak($isRequiredToBreak);

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent();

    /**
     * Set conditions
     *
     * @param string $event
     * @return $this
     */
    public function setEvent($event);

    /**
     * Get conditions
     *
     * @return string
     */
    public function getConditions();

    /**
     * Set conditions
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditions($conditions);

    /**
     * Get actions
     *
     * @return string
     */
    public function getActions();

    /**
     * Set conditions
     *
     * @param string $actions
     * @return $this
     */
    public function setActions($actions);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\AutomationExtensionInterface $extensionAttributes
    );
}
