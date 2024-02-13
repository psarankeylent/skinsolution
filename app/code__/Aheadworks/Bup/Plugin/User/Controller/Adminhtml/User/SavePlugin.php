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
namespace Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User;

use Magento\Framework\Api\DataObjectHelper;
use Magento\User\Controller\Adminhtml\User\Save as SaveUserAction;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterfaceFactory;
use Aheadworks\Bup\Api\Data\UserInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\UserProfile as UserProfileTab;
use Aheadworks\Bup\Model\DataProcessor\PostDataProcessorInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;

/**
 * Class SavePlugin
 *
 * @package Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User
 */
class SavePlugin
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var UserProfileInterfaceFactory
     */
    private $userProfileFactory;

    /**
     * @var PostDataProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param UserProfileInterfaceFactory $userProfileFactory
     * @param PostDataProcessorInterface $postDataProcessor
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        UserProfileInterfaceFactory $userProfileFactory,
        PostDataProcessorInterface $postDataProcessor
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->userProfileFactory = $userProfileFactory;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Process user profile data
     *
     * @param SaveUserAction $subject
     */
    public function beforeExecute(SaveUserAction $subject)
    {
        $postValue = $subject->getRequest()->getPostValue();
        $data = $subject->getRequest()->getParam(FormElementApplier::FORM_DATA_PREFIX);
        if (is_array($data) && !empty($data)) {
            $image = $subject->getRequest()->getParam(FormElementApplier::IMAGE_INPUT_NAME);
            if (isset($image['delete'])) {
                $data[UserProfileInterface::IMAGE . '_delete'] = true;
            }

            $data = $this->postDataProcessor->prepareEntityData($data);

            /** @var UserProfileInterface $userProfile */
            $userProfile = $this->userProfileFactory->create();
            $this->dataObjectHelper->populateWithArray($userProfile, $data, UserProfileInterface::class);
            $postValue[UserInterface::AW_BUP_USER_PROFILE] = $userProfile;
            $subject->getRequest()->setPostValue($postValue);
        }
    }
}
