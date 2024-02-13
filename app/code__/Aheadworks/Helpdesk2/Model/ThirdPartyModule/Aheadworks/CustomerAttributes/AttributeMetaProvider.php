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
namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Form as FormAttributes;
use Magento\Customer\Model\AttributeMetadataResolver;
use Magento\Eav\Model\Config as EavConfig;
use Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes\UsedInFormsPlugin;

/**
 * Class AttributeMetaProvider
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes
 */
class AttributeMetaProvider
{
    private $formElementMapping = [
        'input' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/input',
        'textarea' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/textarea',
        'date' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/date',
        'checkbox' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/single-checkbox',
        'multiselect' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/multiselect',
        'select' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/select',
        'fileUploader' => 'Aheadworks_Helpdesk2/js/ui/form/components/ticket/preview-element/file-uploader'
    ];

    /**
     * @var FormAttributes
     */
    private $formAttributes;

    /**
     * @var AttributeMetadataResolver
     */
    private $attributeMetadataResolver;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var BackendUrlInterface
     */
    private $backendUrl;

    /**
     * @param FormAttributes $formAttributes
     * @param AttributeMetadataResolver $attributeMetadataResolver
     * @param EavConfig $eavConfig
     * @param ArrayManager $arrayManager
     * @param BackendUrlInterface $backendUrl
     */
    public function __construct(
        FormAttributes $formAttributes,
        AttributeMetadataResolver $attributeMetadataResolver,
        EavConfig $eavConfig,
        ArrayManager $arrayManager,
        BackendUrlInterface $backendUrl
    ) {
        $this->formAttributes = $formAttributes;
        $this->eavConfig = $eavConfig;
        $this->attributeMetadataResolver = $attributeMetadataResolver;
        $this->arrayManager = $arrayManager;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Get help desk attributes meta
     *
     * @return array
     * @throws LocalizedException
     */
    public function getHelpDeskAttributesMeta()
    {
        $helpDeskFormAttributes = [];
        $attributes = $this->formAttributes
            ->setFormCode(UsedInFormsPlugin::ADMIN_TICKET_VIEW)
            ->getAllowedAttributes();

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic()) {
                $helpDeskFormAttributes[] = $attribute;
            }
        }

        $meta = [];
        foreach ($helpDeskFormAttributes as $attribute) {
            $attributeMeta = $this->attributeMetadataResolver->getAttributesMeta(
                $attribute,
                $this->eavConfig->getEntityType('customer'),
                false
            );
            $meta[$attribute->getAttributeCode()] = $this->prepareAdditionalMeta($attribute, $attributeMeta);
        }

        return $meta;
    }

    /**
     * Prepare additional meta
     *
     * @param Attribute $attribute
     * @param array $attributeMeta
     * @return array
     */
    private function prepareAdditionalMeta($attribute, $attributeMeta)
    {
        $formElement = $this->arrayManager->get('arguments/data/config/formElement', $attributeMeta);
        return $this->arrayManager->merge(
            'arguments/data/config',
            $attributeMeta,
            $this->preparePreviewMeta($formElement, $attribute->getAttributeCode())
        );
    }

    /**
     * Prepare preview data
     *
     * @param string $formElement
     * @param string $attributeCode
     * @return array
     */
    private function preparePreviewMeta($formElement, $attributeCode)
    {
        $previewMeta = [];
        if (isset($this->formElementMapping[$formElement])) {
            $previewMeta = [
                'component' => $this->formElementMapping[$formElement],
                'requestUrl' => $this->backendUrl->getUrl(
                    'aw_helpdesk2/thirdparty_aheadworks_customerattributes/save'
                ),
                'imports' => [
                    'isEditModeAllowed' => '${ $.provider }:data.is_allowed_to_update_ticket',
                ],
                'payload' => [
                    'email' => '${ $.provider }:data.customer.email',
                    $attributeCode => '${ $.provider }:data.customer.' . $attributeCode,
                    'ticket_id' => '${ $.provider }:data.ticket_id',
                    'ticket_action' => 'update'
                ],
                'service' => [
                    'label' => __('Save'),
                    'buttonClasses' => 'save'
                ],
                'dataScope' => 'customer.' . $attributeCode
            ];
        }

        return $previewMeta;
    }
}
