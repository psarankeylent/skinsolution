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
 * Interface TicketInterface
 * @api
 */
interface TicketOptionInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TICKET_ID = 'ticket_id';
    const LABEL = 'label';
    const VALUE = 'value';

    /**
     * Get option ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set option ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get ticket ID
     *
     * @return int
     */
    public function getTicketId();

    /**
     * Set ticket ID
     *
     * @param int $id
     * @return $this
     */
    public function setTicketId($id);

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
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param int $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketOptionExtensionInterface $extensionAttributes
    );
}
