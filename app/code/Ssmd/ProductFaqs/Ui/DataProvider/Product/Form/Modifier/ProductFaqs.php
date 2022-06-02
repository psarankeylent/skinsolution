<?php

namespace Ssmd\ProductFaqs\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Catalog\Model\Locator\LocatorInterface;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;

/**
 * Data provider for attraction highlights field
 */
class ProductFaqs extends AbstractModifier
{
    const PRODUCT_FAQS_FIELD = 'product_faqs';

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
        $scopeName = ''
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $fieldCode = self::PRODUCT_FAQS_FIELD;

        $model = $this->locator->getProduct();
        $productId = $model->getId();

        $highlightsData = $model->getProductFaqs();

        if ($highlightsData) {
            $highlightsData = json_decode($highlightsData, true);
            $path = $productId . '/' . self::DATA_SOURCE_DEFAULT . '/'. self::PRODUCT_FAQS_FIELD;
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
            self::PRODUCT_FAQS_FIELD,
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
                . '/' . 'product_faqs',                         // This field(name) will post
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
                        'label' => __('FAQ\'s'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'sortOrder' =>
                            $this->arrayManager->get($highlightsPath . '/arguments/data/config/sortOrder', $this->meta),
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
                        'question' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => \Magento\Ui\Component\Form\Element\Input::NAME,
                                        'componentType' => Field::NAME,
                                        //'component' => 'Ssmd_Productfaqs/js/qa',
                                        'dataType' => Text::NAME,
                                        'label' => __('Question'),
                                        'dataScope' => 'question',
                                        'require' => 0,
                                    ],
                                ],
                            ],
                        ],
                        'answer' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => \Magento\Ui\Component\Form\Element\Textarea::NAME,
                                        'rows' => 4,
                                        'cols' => 15,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'label' => __('Answer'),
                                        'dataScope' => 'answer',
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
