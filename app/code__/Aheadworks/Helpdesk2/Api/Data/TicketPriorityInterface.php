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
 * Interface TicketPriorityInterface
 * @api
 */
interface TicketPriorityInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const IS_SYSTEM = 'is_system';
    const LABEL = 'label';

    /**
     * Get ticket status ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ticket status ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get is system
     *
     * @return int
     */
    public function getIsSystem();

    /**
     * Set is system
     *
     * @param int $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketPriorityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketPriorityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketPriorityExtensionInterface $extensionAttributes
    );
}
