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
 * Interface DepartmentOptionInterface
 * @api
 */
interface DepartmentOptionInterface extends ExtensibleDataInterface, StorefrontLabelEntityInterface
{
    /**#@+
     * Department select options group
     */
    const OPTION_GROUP_TEXT = 'text';
    const OPTION_GROUP_SELECT = 'select';
    /**#@-*/

    /**#@+
     * Department field option types
     */
    const OPTION_TYPE_FIELD = 'field';
    const OPTION_TYPE_DROPDOWN = 'dropdown';
    /**#@-*/

    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const DEPARTMENT_ID = 'department_id';
    const TYPE = 'type';
    const SORT_ORDER = 'sort_order';
    const IS_REQUIRED = 'is_required';
    const VALUES = 'values';
    /**#@-*/

    const STOREFRONT_LABEL_ENTITY_TYPE = 'department_option';

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
     * Get department ID
     *
     * @return int
     */
    public function getDepartmentId();

    /**
     * Set department ID
     *
     * @param int $departmentId
     * @return $this
     */
    public function setDepartmentId($departmentId);

    /**
     * Get option type
     *
     * @return string
     */
    public function getType();

    /**
     * Set option type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Is required
     *
     * @return bool
     */
    public function getIsRequired();

    /**
     * Set is required
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired);

    /**
     * Get values
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface[]|null
     */
    public function getValues();

    /**
     * Set values
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface[] $values
     * @return $this
     */
    public function setValues($values);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionExtensionInterface $extensionAttributes
    );
}
