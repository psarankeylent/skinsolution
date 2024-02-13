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
 * Interface RejectingPatternInterface
 *
 * @package Aheadworks\Helpdesk2\Api\Data
 */
interface RejectingPatternInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TITLE = 'title';
    const IS_ACTIVE = 'is_active';
    const SCOPE_TYPES = 'scope_types';
    const PATTERN = 'pattern';
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
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param int $title
     * @return $this
     */
    public function setTitle($title);

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
     * Get scope types
     *
     * @return string[]
     */
    public function getScopeTypes();

    /**
     * Set scope types
     *
     * @param string[] $scopeTypes
     * @return $this
     */
    public function setScopeTypes($scopeTypes);

    /**
     * Get pattern
     *
     * @return string
     */
    public function getPattern();

    /**
     * Set pattern
     *
     * @param string $pattern
     * @return $this
     */
    public function setPattern($pattern);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectingPatternExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectingPatternExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\RejectingPatternExtensionInterface $extensionAttributes
    );
}
