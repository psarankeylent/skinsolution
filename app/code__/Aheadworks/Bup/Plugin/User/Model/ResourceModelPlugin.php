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
namespace Aheadworks\Bup\Plugin\User\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\User\Api\Data\UserInterface;
use Magento\User\Model\ResourceModel\User as UserResource;
use Aheadworks\Bup\Api\UserProfileRepositoryInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterface;

/**
 * Class ResourceModelPlugin
 *
 * @package Aheadworks\Bup\Plugin\User\Model
 */
class ResourceModelPlugin
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @param UserProfileRepositoryInterface $userProfileRepository
     */
    public function __construct(
        UserProfileRepositoryInterface $userProfileRepository
    ) {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * Save user profile after saving user data
     *
     * @param UserResource $subject
     * @param UserResource $result
     * @param UserInterface $user
     * @return UserResource
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        UserResource $subject,
        UserResource $result,
        UserInterface $user
    ) {
        /** @var UserProfileInterface $userProfile */
        $userProfile = $user->getAwBupUserProfile();
        if ($userProfile) {
            $userProfile->setUserId($user->getId());
            $this->userProfileRepository->save($userProfile);
        }

        return $result;
    }
}
