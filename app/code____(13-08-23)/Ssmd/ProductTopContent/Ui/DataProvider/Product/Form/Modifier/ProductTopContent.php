<?php

namespace Ssmd\ProductTopContent\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\UrlInterface;

/**
 * Data provider for attraction highlights field
 */
class ProductTopContent extends AbstractModifier
{
    const PRODUCT_TOPCONTENT_FIELD = 'product_topcontent';

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
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        $scopeName = '',
        UrlInterface  $urlBuilder
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $fieldCode = self::PRODUCT_TOPCONTENT_FIELD;

        $model = $this->locator->getProduct();
        $productId = $model->getId();

        $highlightsData = $model->getProductTopcontent();

        if ($highlightsData) {
            $highlightsData = json_decode($highlightsData, true);

            $path = $productId . '/' . self::DATA_SOURCE_DEFAULT . '/'. self::PRODUCT_TOPCONTENT_FIELD;
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
            self::PRODUCT_TOPCONTENT_FIELD,
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
                . '/' . 'product_topcontent',                         // This field(name) will post
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
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Top Content'),
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
                                        'formElement' => 'imageUploader',
                                        'componentType' => 'imageUploader',
                                        'label' => __('Top Section'),
                                        'dataScope' => 'content_section',
                                        'component' => 'Magento_Ui/js/form/element/image-uploader',
                                        'elementTmpl' => 'Magento_Ui/components/image-uploader',
                                        'previewTmpl' => 'Ssmd_ProductTopContent/image-preview',
                                        'allowedExtensions' => 'jpg jpeg gif png',

                                        'uploaderConfig' => [
                                            'url' => $this->urlBuilder->getUrl(
                                                'topcontent/image/upload'
                                            ),
                                        ],
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
