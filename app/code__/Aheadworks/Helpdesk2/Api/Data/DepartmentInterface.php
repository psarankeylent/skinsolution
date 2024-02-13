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
 * Interface DepartmentInterface
 * @api
 */
interface DepartmentInterface extends ExtensibleDataInterface, StorefrontLabelEntityInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const IS_ACTIVE = 'is_active';
    const IS_ALLOW_GUEST = 'is_allow_guest';
    const SORT_ORDER = 'sort_order';
    const PRIMARY_AGENT_ID = 'primary_agent_id';
    const AGENT_IDS = 'agent_ids';
    const STORE_IDS = 'store_ids';
    const OPTIONS = 'options';
    const PERMISSIONS = 'permissions';
    const GATEWAY_IDS = 'gateway_ids';
    /**#@-*/

    const STOREFRONT_LABEL_ENTITY_TYPE = 'department';

    /**
     * Get department ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set department ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get department name
     *
     * @return string
     */
    public function getName();

    /**
     * Set department name
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
     * Get is allow guest
     *
     * @return bool
     */
    public function getIsAllowGuest();

    /**
     * Set is allow guest
     *
     * @param bool $isAllowGuest
     * @return $this
     */
    public function setIsAllowGuest($isAllowGuest);

    /**
     * Get department sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set department sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get primary agent ID
     *
     * @return int|null
     */
    public function getPrimaryAgentId();

    /**
     * Set primary agent ID
     *
     * @param int|null $agentId
     * @return $this
     */
    public function setPrimaryAgentId($agentId);

    /**
     * Get agent IDs
     *
     * @return int[]
     */
    public function getAgentIds();

    /**
     * Set agent IDs
     *
     * @param int[] $agentIds
     * @return $this
     */
    public function setAgentIds($agentIds);

    /**
     * Get store IDs
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set store IDs
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get options
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface[]
     */
    public function getOptions();

    /**
     * Set options
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options);

    /**
     * Get permissions
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface
     */
    public function getPermissions();

    /**
     * Set permissions
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface $permissions
     * @return $this
     */
    public function setPermissions($permissions);

    /**
     * Get gateway IDs
     *
     * @return int[]
     */
    public function getGatewayIds();

    /**
     * Set gateway IDs
     *
     * @param int[] $gatewayIds
     * @return $this
     */
    public function setGatewayIds($gatewayIds);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentExtensionInterface $extensionAttributes
    );
}
