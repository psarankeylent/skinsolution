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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department
 */
class Gateway implements ProcessorInterface
{
    const GATEWAY_SELECT_FIELD = 'gateway_id';

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $gatewayIds = $data[self::GATEWAY_SELECT_FIELD]
            ? [$data[self::GATEWAY_SELECT_FIELD]]
            : [];
        $data[DepartmentInterface::GATEWAY_IDS] = $gatewayIds;

        return $data;
    }
}
