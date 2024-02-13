<?php
namespace Ssmd\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;


/**
 * BgColor Class
 */
class BgColor extends AbstractModifier
{
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta['product-details']['children']['bg_color'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'colorPicker',
                        'componentType' => Field::NAME,
                        'label' => __('Page Background Color'),
                        'dataScope' => 'bg_color',
                        'scopeLabel' => '[GLOBAL]',
                        'elementTmpl' => 'ui/form/element/color-picker',
                        'colorFormat' => 'hex',
                        'colorPickerMode' => 'noalpha',
                        'validation' => ['validate-color' => true],
                    ]
                ]
            ]
        ];

        return $meta;
    }
}
