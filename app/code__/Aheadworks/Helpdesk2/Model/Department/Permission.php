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
namespace Aheadworks\Helpdesk2\Model\Department;

use Magento\Framework\Api\AbstractSimpleObject;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;

/**
 * Class Permission
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class Permission extends AbstractSimpleObject implements DepartmentPermissionInterface
{
    /**
     * @inheritdoc
     */
    public function getViewRoleIds()
    {
        return $this->_get(self::VIEW_ROLE_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setViewRoleIds($roleIds)
    {
        return $this->setData(self::VIEW_ROLE_IDS, $roleIds);
    }

    /**
     * @inheritdoc
     */
    public function getUpdateRoleIds()
    {
        return $this->_get(self::UPDATE_ROLE_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setUpdateRoleIds($roleIds)
    {
        return $this->setData(self::UPDATE_ROLE_IDS, $roleIds);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
