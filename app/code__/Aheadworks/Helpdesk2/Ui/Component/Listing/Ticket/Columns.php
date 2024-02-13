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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns as ListingColumns;
use Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Attribute\Provider as GridAttributeProvider;

/**
 * Class Columns
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket
 */
class Columns extends ListingColumns
{
    const COLUMN_SORT_ORDER_TO_START = 200;

    /**
     * @var GridAttributeProvider
     */
    private $gridAttributeProvider;

    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    /**
     * @var array
     */
    protected $filterMap = [
        'default' => 'text',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'select',
        'date' => 'dateRange',
    ];

    /**
     * @param ContextInterface $context
     * @param GridAttributeProvider $gridAttributeProvider
     * @param ColumnFactory $columnFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        GridAttributeProvider $gridAttributeProvider,
        ColumnFactory $columnFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->gridAttributeProvider = $gridAttributeProvider;
        $this->columnFactory = $columnFactory;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $columnSortOrder = self::COLUMN_SORT_ORDER_TO_START;
        foreach ($this->gridAttributeProvider->getList() as $attribute) {
            $config = [];
            if (!isset($this->components[$attribute->getAttributeCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                if ($attribute->getIsFilterableInGrid()) {
                    $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                }
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
        parent::prepare();
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    private function getFilterType($frontendInput)
    {
        return $this->filterMap[$frontendInput] ?? $this->filterMap['default'];
    }
}
