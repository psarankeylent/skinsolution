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
namespace Aheadworks\Helpdesk2\Model\Department\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class GatewayResolver
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Search
 */
class GatewayResolver
{
    /**
     * @var DepartmentResourceModel
     */
    private $departmentResource;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @param DepartmentResourceModel $departmentResource
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        DepartmentResourceModel $departmentResource,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        $this->departmentResource = $departmentResource;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * Resolve gateway for department ID
     *
     * @param int $departmentId
     * @return GatewayDataInterface|null
     * @throws LocalizedException
     */
    public function resolveGatewayForDepartmentId($departmentId)
    {
        $gatewayIds = $this->departmentResource->getGatewayIdsForDepartment($departmentId);
        $gateway = null;
        if (!empty($gatewayIds)) {
            $gatewayId = reset($gatewayIds);
            try {
                $gateway = $this->gatewayRepository->get($gatewayId);
            } catch (NoSuchEntityException $exception) {
                $gateway = null;
            }
        }

        return $gateway;
    }
}
