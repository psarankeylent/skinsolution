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

use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GuestChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class GuestChecker
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $repository;

    /**
     * @param UserContextInterface $userContext
     * @param DepartmentRepositoryInterface $repository
     */
    public function __construct(
        UserContextInterface $userContext,
        DepartmentRepositoryInterface $repository
    ) {
        $this->userContext = $userContext;
        $this->repository = $repository;
    }

    /**
     * Check if the department can be used by a guest
     *
     * @param int $departmentId
     * @return bool
     * @throws LocalizedException
     */
    public function canBeUsedByGuest($departmentId)
    {
        $department = $this->repository->get($departmentId);
        $isGuest = $this->userContext->getUserType() != $this->userContext::USER_TYPE_CUSTOMER;

        return !($isGuest && !$department->getIsAllowGuest());
    }
}
