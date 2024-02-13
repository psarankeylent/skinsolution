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

use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Class FormElementApplier
 *
 * @package Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab
 */
class FormElementApplier
{
    /**
     * Form data prefix
     *
     * Used to collect user profile data
     */
    const FORM_DATA_PREFIX = 'aw_bup';

    /**
     * Image input name
     *
     * Name of image loader HTML element
     */
    const IMAGE_INPUT_NAME = 'aw_bup_image';

    /**
     * Apply profile status to $fieldset
     *
     * @param Fieldset $fieldset
     */
    public function applyStatusField($fieldset)
    {
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::STATUS,
            'select',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::STATUS),
                'label' => __('User Status'),
                'title' => __('User Status'),
                'id' => 'status',
                'options' => [
                    '0' => __('Inactive'),
                    '1' => __('Active')
                ]
            ]
        );
    }

    /**
     * Apply sort order field to $fieldset
     *
     * @param Fieldset $fieldset
     */
    public function applySortOrderField($fieldset)
    {
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::SORT_ORDER,
            'text',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::SORT_ORDER),
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'id' => 'sort_order',
                'class' => 'validate-integer validate-not-negative-number'
            ]
        );
    }

    /**
     * Apply profile detail fields to $fieldset
     *
     * @param Fieldset $fieldset
     */
    public function applyDetailFields($fieldset)
    {
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::DISPLAY_NAME,
            'text',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::DISPLAY_NAME),
                'label' => __('Display Name'),
                'title' => __('Display Name'),
                'id' => 'display_name',
                'class' => 'validate-length maximum-length-90'
            ]
        );
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::EMAIL,
            'text',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::EMAIL),
                'label' => __('Email'),
                'title' => __('Email'),
                'id' => 'email',
                'class' => 'validate-email'
            ]
        );
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::PHONE_NUMBER,
            'text',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::PHONE_NUMBER),
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'id' => 'phone_number',
                'class' => 'aw_bup-validate-phone'
            ]
        );
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::IMAGE . '_loader',
            'image',
            [
                'name' => self::IMAGE_INPUT_NAME,
                'label' => __('Image'),
                'title' => __('Image'),
                'id' => 'profile-image',
                'css_class' => 'aw_bup-profile-image',
                'note' => __('Allowed jpg, jpeg, gif and png file types')
            ]
        );
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::IMAGE,
            'hidden',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::IMAGE)
            ]
        );
        $fieldset->addField(
            self::FORM_DATA_PREFIX . '_' . UserProfileInterface::ADDITIONAL_INFORMATION,
            'textarea',
            [
                'name' => $this->prepareFieldName(UserProfileInterface::ADDITIONAL_INFORMATION),
                'label' => __('Additional Information'),
                'title' => __('Additional Information'),
                'id' => 'additional_information',
                'class' => 'validate-length maximum-length-270'
            ]
        );
    }

    /**
     * Prepare form field name
     *
     * @param string $fieldName
     * @return string
     */
    private function prepareFieldName($fieldName)
    {
        return sprintf("%s[%s]", self::FORM_DATA_PREFIX, $fieldName);
    }
}
