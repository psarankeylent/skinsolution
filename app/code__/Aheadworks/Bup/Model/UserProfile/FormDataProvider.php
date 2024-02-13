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
namespace Aheadworks\Bup\Model\UserProfile;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterfaceFactory;
use Aheadworks\Bup\Api\UserProfileRepositoryInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Bup\Model\UserProfile
 */
class FormDataProvider
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param ImageInfo $imageInfo
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        UserProfileInterfaceFactory $userProfileFactory,
        UserProfileRepositoryInterface $userProfileRepository,
        ImageInfo $imageInfo
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->userProfileFactory = $userProfileFactory;
        $this->userProfileRepository = $userProfileRepository;
        $this->imageInfo = $imageInfo;
    }

    /**
     * Prepare existing profile data or create a new one
     *
     * @param UserProfileInterface|array $userProfile
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareFormData($userProfile)
    {
        if ($userProfile instanceof UserProfileInterface) {
            $data = $this->dataObjectProcessor->buildOutputDataArray(
                $userProfile,
                UserProfileInterface::class
            );
        } else {
            $data = $userProfile;
        }
        $data = $this->prepareData($data);

        $formData = [];
        foreach ($data as $key => $value) {
            $formData[FormElementApplier::FORM_DATA_PREFIX . '_' . $key] = $value;
        }

        return $formData;
    }

    /**
     * Get user profile
     *
     * @param int|null $userId
     * @return UserProfileInterface
     */
    public function getUserProfile($userId)
    {
        try {
            $userProfile = $this->userProfileRepository->get($userId);
        } catch (NoSuchEntityException $e) {
            $userProfile = $this->createNewUserProfile();
        }

        return $userProfile;
    }

    /**
     * Create new user profile
     *
     * @return UserProfileInterface
     */
    private function createNewUserProfile()
    {
        return $this->userProfileFactory->create();
    }

    /**
     * Prepare form data
     *
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareData($data)
    {
        if (isset($data[UserProfileInterface::IMAGE]) && !empty($data[UserProfileInterface::IMAGE])) {
            $image = $data[UserProfileInterface::IMAGE];
            $data[UserProfileInterface::IMAGE . '_loader'] = $this->imageInfo->getMediaUrl($image);
        }

        return $data;
    }
}
