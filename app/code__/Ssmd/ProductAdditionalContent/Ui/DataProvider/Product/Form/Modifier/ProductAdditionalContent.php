<?php

namespace Ssmd\ProductAdditionalContent\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;
use Ssmd\ProductAdditionalContent\Helper\Data;

/**
 * Data provider for attraction highlights field
 */
class ProductAdditionalContent extends AbstractModifier
{
    const PRODUCT_ADDITIONALCONTENT_FIELD = 'product_additionalcontent';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
                         $scopeName = '',
        Data $helper
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $fieldCode = self::PRODUCT_ADDITIONALCONTENT_FIELD;

        $model = $this->locator->getProduct();
        $productId = $model->getId();

        $highlightsData = $model->getProductAdditionalcontent();

        if ($highlightsData) {
            $highlightsData = json_decode($highlightsData, true);
            $path = $productId . '/' . self::DATA_SOURCE_DEFAULT . '/'. self::PRODUCT_ADDITIONALCONTENT_FIELD;
            $data = $this->arrayManager->set($path, $data, $highlightsData);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this -> initAttractionHighlightFields();
        return $this->meta;
    }

    /**
     * Customize attraction highlights field
     *
     * @return $this
     */
    protected function initAttractionHighlightFields()
    {
        $highlightsPath = $this->arrayManager->findPath(
            self::PRODUCT_ADDITIONALCONTENT_FIELD,
            $this->meta,
            null,
            'children'
        );

        if ($highlightsPath) {
            $this->meta = $this->arrayManager->merge(
                $highlightsPath,
                $this->meta,
                $this->initHighlightFieldStructure($highlightsPath)
            );

            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($highlightsPath, 0, -3)
                . '/' . 'product_additionalcontent',                         // This field(name) will post
                $this->meta,
                $this->arrayManager->get($highlightsPath, $this->meta)
            //$productQAButton
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($highlightsPath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }


    /**
     * Get attraction highlights dynamic rows structure
     *
     * @param string $highlightsPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function initHighlightFieldStructure($highlightsPath)
    {
        //echo "<pre>"; print_r($this->meta); exit;
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Additional Content'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'sortOrder' => 100,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'content_section' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => \Magento\Ui\Component\Form\Element\Select::NAME,
                                        'componentType' => Field::NAME,
                                        //'dataType' => Text::NAME,
                                        /*'options' => [
                                            ['value' => '', 'label' => '-- Select --'],
                                            ['value' => 'test_value_1', 'label' => 'Test Value 1'],
                                            ['value' => 'test_value_2', 'label' => 'Test Value 2'],
                                            ['value' => 'test_value_3', 'label' => 'Test Value 3'],
                                            ['value' => 'add-new', 'label' => '-- Add New Section --'],
                                        ],
                                        */
                                        'options' => $this->helper->getProductContentSectionOptions(),
                                        'label' => __('Content Section'),
                                        'dataScope' => 'content_section',
                                        'require' => 0,
                                    ],
                                ],
                            ],
                        ],
                        'content_html' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => \Magento\Ui\Component\Form\Element\Wysiwyg::NAME,
                                        'wysiwyg' => true,
                                        'dynamic_id' => true,
                                        'wysiwygConfigData' => [
                                            'height' => '200px',
                                            'dynamic_id' => true,
                                        ],
                                        'rows' => 5,
                                        'componentType' => Field::NAME,
                                        'dataType' => Textarea::NAME,
                                        'label' => __('Content HTML'),
                                        'dataScope' => 'content_html',
                                        'require' => 0,
                                    ],
                                ],
                            ],
                        ],
                        'hidden_section' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => \Magento\Ui\Component\Form\Element\Hidden::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'hidden_section',
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
