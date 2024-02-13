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
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Gateway extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_gateway';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, GatewayDataInterface::ID);
    }

    /**
     * Get department ID for gateway ID
     *
     * @param int $gatewayId
     * @return int|false
     * @throws LocalizedException
     */
    public function getDepartmentIdForGateway($gatewayId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'dep_gate_tbl.department_id')
            ->joinLeft(
                ['dep_gate_tbl' => $this->getTable(Department::DEPARTMENT_GATEWAY_TABLE_NAME)],
                'id = dep_gate_tbl.gateway_id',
                []
            )
            ->where('id = ?', $gatewayId);

        return $connection->fetchOne($select);
    }
}
