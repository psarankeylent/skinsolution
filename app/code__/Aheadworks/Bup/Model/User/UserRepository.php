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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Model\User;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\User\Model\ResourceModel\User as UserResource;

/**
 * Class UserRepository
 *
 * @package Aheadworks\Bup\Model\User
 */
class UserRepository
{
    /**
     * @var UserResource
     */
    private $resource;

    /**
     * @var UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param UserResource $resource
     * @param UserInterfaceFactory $userFactory
     */
    public function __construct(
        UserResource $resource,
        UserInterfaceFactory $userFactory
    ) {
        $this->resource = $resource;
        $this->userFactory = $userFactory;
    }

    /**
     * Get Magento backend user by ID
     *
     * @param int $userId
     * @return UserInterface
     * @throws NoSuchEntityException
     */
    public function getById($userId)
    {
        if (!isset($this->registry[$userId])) {
            /** @var UserInterface $user */
            $user = $this->userFactory->create();
            $this->resource->load($user, $userId);
            if (!$user->getId()) {
                throw NoSuchEntityException::singleField('id', $userId);
            }
            $this->registry[$userId] = $user;
        }

        return $this->registry[$userId];
    }
}
