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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters\FilterModifier;
use Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Attribute as AttributeModel;

/**
 * Class ColumnFactory
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket
 */
class ColumnFactory
{
    /**
     * @var UiComponentFactory
     */
    private $componentFactory;

    /**
     * @var array
     */
    private $jsComponentMap = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    private $dataTypeMap = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'multiselect',
        'date' => 'date',
    ];

    /**
     * @param UiComponentFactory $componentFactory
     */
    public function __construct(UiComponentFactory $componentFactory)
    {
        $this->componentFactory = $componentFactory;
    }

    /**
     * Create column
     *
     * @param TicketAttributeInterface|AttributeModel $attribute
     * @param ContextInterface $context
     * @param array $config
     *
     * @return UiComponentInterface
     * @throws LocalizedException
     */
    public function create($attribute, $context, array $config = [])
    {
        $filterModifiers = $context->getRequestParam(FilterModifier::FILTER_MODIFIER, []);

        $columnName = $attribute->getAttributeCode();
        $config = array_merge(
            [
                'label' => __($attribute->getDefaultFrontendLabel()),
                'dataType' => $this->getDataType($attribute),
                'add_field' => true,
                'visible' => true,
                'filter' => ($attribute->getIsFilterableInGrid() || array_key_exists($columnName, $filterModifiers))
                    ? $this->getFilterType($attribute->getFrontendInput())
                    : null,
                '__disableTmpl' => ['label' => true],
            ],
            $config
        );

        if ($attribute->usesSource()) {
            $config['options'] = $attribute->getSource()->getAllOptions();
            foreach ($config['options'] as &$optionData) {
                $optionData['__disableTmpl'] = true;
            }
        }
        
        $config['component'] = $this->getJsComponent($config['dataType']);
        
        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];
        
        return $this->componentFactory->create($columnName, 'column', $arguments);
    }

    /**
     * Get Js Component
     *
     * @param string $dataType
     *
     * @return string
     */
    protected function getJsComponent($dataType)
    {
        return $this->jsComponentMap[$dataType];
    }

    /**
     * Get attribute data type
     *
     * @param TicketAttributeInterface $attribute
     * @return string
     */
    protected function getDataType($attribute)
    {
        return $this->dataTypeMap[$attribute->getFrontendInput()] ?? $this->dataTypeMap['default'];
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        $filtersMap = ['date' => 'dateRange'];
        $result = array_replace_recursive($this->dataTypeMap, $filtersMap);

        return $result[$frontendInput] ?? $result['default'];
    }
}
