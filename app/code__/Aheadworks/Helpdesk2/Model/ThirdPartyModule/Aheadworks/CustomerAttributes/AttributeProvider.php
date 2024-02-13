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

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Form as FormAttributes;
use Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes\UsedInFormsPlugin;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker;
use Aheadworks\Helpdesk2\Model\Source\Automation\OperatorSource;
use Aheadworks\Helpdesk2\Model\Automation\ValueMapper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AttributeProvider
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes
 */
class AttributeProvider
{
    const ATTRIBUTE_CODE_PREFIX = 'aw_cust_attr:';

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    /**
     * @var FormAttributes
     */
    private $formAttributes;

    /**
     * @var OperatorSource
     */
    private $operatorSource;

    /**
     * @var array
     */
    private $allowedInputTypes = [
        'textarea',
        'multiselect',
        'text',
        'boolean',
        'select'
    ];

    /**
     * @var array
     */
    private $conditions;

    /**
     * @var array
     */
    private $operators;

    /**
     * @var array
     */
    private $values;

    /**
     * @param FormAttributes $formAttributes
     * @param ModuleChecker $moduleChecker
     * @param OperatorSource $operatorSource
     */
    public function __construct(
        FormAttributes $formAttributes,
        ModuleChecker $moduleChecker,
        OperatorSource $operatorSource
    ) {
        $this->formAttributes = $formAttributes;
        $this->moduleChecker = $moduleChecker;
        $this->operatorSource = $operatorSource;
    }

    /**
     * Prepare automation conditions
     *
     * @return array
     * @throws LocalizedException
     */
    public function prepareAutomationConditions()
    {
        if ($this->conditions === null) {
            $this->prepareData();
        }

        return $this->conditions;
    }

    /**
     * Prepare automation operators
     *
     * @return array
     * @throws LocalizedException
     */
    public function prepareAutomationOperators()
    {
        if ($this->operators === null) {
            $this->prepareData();
        }

        return $this->operators;
    }

    /**
     * Prepare automation values
     *
     * @return array
     * @throws LocalizedException
     */
    public function prepareAutomationValues()
    {
        if ($this->values === null) {
            $this->prepareData();
        }

        return $this->values;
    }

    /**
     * Get help desk attributes data
     *
     * @return boolean
     * @throws LocalizedException
     */
    public function prepareData()
    {
        $this->conditions = [];
        $this->operators = [];
        $this->values = [];
        if (!$this->moduleChecker->isAwCustomerAttributesEnabled()) {
            return false;
        }

        $attributes = $this->formAttributes
            ->setFormCode(UsedInFormsPlugin::ADMIN_TICKET_VIEW)
            ->getAllowedAttributes();

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic() && in_array($attribute->getFrontendInput(), $this->allowedInputTypes)) {
                $value = self::ATTRIBUTE_CODE_PREFIX . $attribute->getAttributeCode();
                $this->conditions[] = [
                    'value' => $value,
                    'label' => __('Customer Attribute: %1', $attribute->getStoreLabel())
                ];
                $this->operators[$value] = $this->resolveOperator($attribute);
                $this->values[$value] = $this->resolveValue($attribute);
            }
        }

        return true;
    }

    /**
     * Resolve operator
     *
     * @param Attribute $attribute
     * @return array
     * @throws LocalizedException
     */
    private function resolveOperator($attribute)
    {
        switch ($attribute->getFrontendInput()) {
            case 'text':
                return $this->operatorSource->getLikeOperator();
            case 'textarea':
                return $this->operatorSource->getLikeOperator();
            case 'multiselect':
                return $this->operatorSource->getInOperator();
            case 'boolean':
                return $this->operatorSource->getEqualOperator();
            case 'select':
                return $this->operatorSource->getEqualOperator();
        }

        throw new LocalizedException(__('Attribute type: %1 is not supported'), $attribute->getFrontendInput());
    }

    /**
     * Resolve value
     *
     * @param Attribute $attribute
     * @return array
     * @throws LocalizedException
     */
    private function resolveValue($attribute)
    {
        switch ($attribute->getFrontendInput()) {
            case 'text':
                return [
                    'type' => ValueMapper::TEXT
                ];
            case 'textarea':
                return [
                    'type' => ValueMapper::TEXTAREA
                ];
            case 'multiselect':
                return [
                    'type' => ValueMapper::MULTISELECT,
                    'options' => $attribute->getSource()->getAllOptions()
                ];
            case 'boolean':
                return [
                    'type' => ValueMapper::BOOLEAN
                ];
            case 'select':
                return [
                    'type' => ValueMapper::SELECT,
                    'options' => $attribute->getSource()->getAllOptions()
                ];
        }

        throw new LocalizedException(__('Attribute type: %1 is not supported'), $attribute->getFrontendInput());
    }
}
