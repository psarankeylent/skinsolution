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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field as FormField;
use Magento\Ui\Component\Form\Element\DataType\Number as NumberDataType;
use Magento\Ui\Component\Form\Element\DataType\Text as TextDataType;
use Magento\Ui\Component\Form\Element\Input as InputFormElement;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelEntityInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Ui\Component\Listing\Columns\Store\Options as StoreOptionsSource;

/**
 * Class Option
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department
 */
class Option implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreOptionsSource
     */
    private $storeOptionsSource;

    /**
     * @param ArrayManager $arrayManager
     * @param StoreManagerInterface $storeManager
     * @param StoreOptionsSource $storeOptionsSource
     */
    public function __construct(
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        StoreOptionsSource $storeOptionsSource
    ) {
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        $this->storeOptionsSource = $storeOptionsSource;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[DepartmentInterface::OPTIONS]) && is_array($data[DepartmentInterface::OPTIONS])) {
            foreach ($data[DepartmentInterface::OPTIONS] as &$option) {
                $option[DepartmentOptionInterface::IS_REQUIRED] = $option[DepartmentOptionInterface::IS_REQUIRED]
                    ? '1'
                    : '0';
                if (!isset($option[DepartmentOptionInterface::VALUES])
                    || !is_array($option[DepartmentOptionInterface::VALUES])
                ) {
                    continue;
                }

                foreach ($option[DepartmentOptionInterface::VALUES] as &$optionValue) {
                    $optionValueLabels = [];
                    if (isset($optionValue[StorefrontLabelEntityInterface::STOREFRONT_LABELS])
                        && is_array($optionValue[StorefrontLabelEntityInterface::STOREFRONT_LABELS])
                    ) {
                        foreach ($optionValue[StorefrontLabelEntityInterface::STOREFRONT_LABELS] as $optionValueLabel) {
                            $optionValueLabels[$optionValueLabel[StorefrontLabelInterface::STORE_ID]] =
                                $optionValueLabel[StorefrontLabelInterface::CONTENT];
                        }
                    }
                    $optionValue[StorefrontLabelEntityInterface::STOREFRONT_LABELS] = $optionValueLabels;
                }
            }

        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        $path = 'storefront_required_information/children/options/' .
            'children/record/children/option_fieldset/children/values/children/record';

        $meta = $this->arrayManager->set(
            $path,
            $meta,
            [
                'children' => $this->getMultipleElementOptionsChildren()
            ]
        );

        return $meta;
    }

    /**
     * Retrieve multiple child element options
     *
     * @return array
     */
    private function getMultipleElementOptionsChildren()
    {
        $children = [];
        if ($this->storeManager->hasSingleStore()) {
            $value = Store::DEFAULT_STORE_ID;
            $children['store_label_' . $value] = $this->getOptionFieldConfig($value, __('Value Title'));
        } else {
            foreach ($this->storeOptionsSource->getStoreList() as $option) {
                $children['store_label_' . $option['value']] =
                    $this->getOptionFieldConfig($option['value'], $option['label']);
            }
        }

        $children = array_merge(
            $children,
            [
                'sort_order' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => FormField::NAME,
                                'dataType' => NumberDataType::NAME,
                                'dataScope' => 'sort_order',
                                'formElement' => InputFormElement::NAME,
                                'visible' => false,
                                'additionalClasses' => [
                                    '_hidden' => true
                                ]
                            ],
                        ],
                    ],
                ],
                'action_delete' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => ActionDelete::NAME,
                                'dataType' => TextDataType::NAME,
                                'fit' => true
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $children;
    }

    /**
     * Retrieve option field config
     *
     * @param int $storeId
     * @param string $label
     * @return array
     */
    private function getOptionFieldConfig($storeId, $label)
    {
        $validation = ($storeId == Store::DEFAULT_STORE_ID)
            ? ['validation' => ['required-entry' => true]]
            : [];

        return [
            'arguments' => [
                'data' => [
                    'config' => array_merge(
                        [
                            'componentType' => FormField::NAME,
                            'dataType' => TextDataType::NAME,
                            'dataScope' => 'storefront_labels.' . $storeId,
                            'formElement' => InputFormElement::NAME,
                            'label' => $label
                        ],
                        $validation
                    )
                ],
            ],
        ];
    }
}
