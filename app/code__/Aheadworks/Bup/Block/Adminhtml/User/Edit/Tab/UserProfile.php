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
namespace Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab;

use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\User\Model\User as MagentoUser;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\UserProfile\FormDataProvider;

/**
 * Class UserProfile
 *
 * @package Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab
 */
class UserProfile extends Generic implements TabInterface
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
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FormDataProvider $formDataProvider
     * @param FormElementApplier $formElementApplier
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FormDataProvider $formDataProvider,
        FormElementApplier $formElementApplier,
        array $data = []
    ) {
        $this->formDataProvider = $formDataProvider;
        $this->formElementApplier = $formElementApplier;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return $this
     * @throws LocalizedException
     */
    public function _prepareForm()
    {
        /** @var $userModel MagentoUser */
        $userModel = $this->_coreRegistry->registry('permissions_user');

        /**@var Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('user_profile_data', ['legend' => __('Additional User Information')]);
        $userId = $this->getRequest()->getParam(UserProfileInterface::USER_ID);
        $userProfile = $this->formDataProvider->getUserProfile($userId);
        $this->formElementApplier->applyStatusField($fieldset);
        $this->formElementApplier->applySortOrderField($fieldset);
        $this->formElementApplier->applyDetailFields($fieldset);

        $persistedData = $userModel->getData(FormElementApplier::FORM_DATA_PREFIX);
        if ($persistedData) {
            $image = $userModel->getData(FormElementApplier::IMAGE_INPUT_NAME);
            if (isset($image['delete'])) {
                $persistedData[UserProfileInterface::IMAGE] = '';
            }
            $data = $this->formDataProvider->prepareFormData($persistedData);
        } else {
            $data = $this->formDataProvider->prepareFormData($userProfile);
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Additional User Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
