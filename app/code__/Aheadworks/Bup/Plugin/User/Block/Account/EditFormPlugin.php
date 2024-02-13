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
namespace Aheadworks\Bup\Plugin\User\Block\Account;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Data\Form;
use Magento\Backend\Block\System\Account\Edit\Form as EditForm;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\UserProfile\FormDataProvider;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;
use Aheadworks\Bup\Model\User\UserRepository;

/**
 * Class EditFormPlugin
 *
 * @package Aheadworks\Bup\Plugin\User\Block\Account
 */
class EditFormPlugin
{
    /**
     * @var FormDataProvider
     */
    private $formDataProvider;

    /**
     * @var FormElementApplier
     */
    private $formElementApplier;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param FormDataProvider $formDataProvider
     * @param FormElementApplier $formElementApplier
     * @param AuthSession $authSession
     * @param UserRepository $userRepository
     */
    public function __construct(
        FormDataProvider $formDataProvider,
        FormElementApplier $formElementApplier,
        AuthSession $authSession,
        UserRepository $userRepository
    ) {
        $this->formDataProvider = $formDataProvider;
        $this->formElementApplier = $formElementApplier;
        $this->authSession = $authSession;
        $this->userRepository = $userRepository;
    }

    /**
     * Add user profile fieldset to account edit form
     *
     * @param EditForm $subject
     * @param Form $form
     * @return array
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetForm($subject, $form)
    {
        $userId = $this->authSession->getUser()->getId();
        $userProfile = $this->formDataProvider->getUserProfile($userId);

        if ($userProfile->getUserId() && $userProfile->getStatus()) {
            $this->addUserProfileToForm($userProfile, $form);
        }

        return [$form];
    }

    /**
     * Add user profile data to form
     *
     * @param UserProfileInterface $userProfile
     * @param Form $form
     * @throws NoSuchEntityException
     */
    private function addUserProfileToForm($userProfile, $form)
    {
        $fieldset = $form->addFieldset('user_profile_fieldset', ['legend' => __('Additional User Information')]);
        $this->formElementApplier->applyDetailFields($fieldset);

        $userModel = $this->authSession->getUser();
        $persistedData = $userModel->getData(FormElementApplier::FORM_DATA_PREFIX);

        $userProfileData = $persistedData
            ? $this->formDataProvider->prepareFormData($persistedData)
            : $this->formDataProvider->prepareFormData($userProfile);

        $user = $this->userRepository->getById($userModel->getId());
        $user->unsetData('password');
        $data = array_merge($user->getData(), $userProfileData);
        unset($data[EditForm::IDENTITY_VERIFICATION_PASSWORD_FIELD]);
        $form->setValues($data);
    }
}
