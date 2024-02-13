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
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Department extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_department';
    const DEPARTMENT_STORE_TABLE_NAME = 'aw_helpdesk2_department_store';
    const DEPARTMENT_PERMISSION_TABLE_NAME = 'aw_helpdesk2_department_permission';
    const DEPARTMENT_AGENT_TABLE_NAME = 'aw_helpdesk2_department_agent';
    const DEPARTMENT_GATEWAY_TABLE_NAME = 'aw_helpdesk2_department_gateway';
    const DEPARTMENT_OPTION_TABLE_NAME = 'aw_helpdesk2_department_option';
    const DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME = 'aw_helpdesk2_department_option_type_value';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, DepartmentInterface::ID);
    }

    /**
     * Delete an object
     *
     * @param AbstractModel $object
     * @return AbstractResourceModel
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $object->beforeDelete();
        return parent::delete($object);
    }

    /**
     * Get gateway IDs for department ID
     *
     * @param int $departmentId
     * @return array
     * @throws LocalizedException
     */
    public function getGatewayIdsForDepartment($departmentId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'dep_gate_tbl.gateway_id')
            ->joinLeft(
                ['dep_gate_tbl' => $this->getTable(Department::DEPARTMENT_GATEWAY_TABLE_NAME)],
                'id = dep_gate_tbl.department_id',
                []
            )
            ->where('id = ?', $departmentId);

        $result = $connection->fetchCol($select);
        if (!reset($result)) {
            $result = [];
        }

        return $result;
    }
}
