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
namespace Aheadworks\Helpdesk2\Model\Source\Automation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class OperatorSource
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Automation
 */
class OperatorSource implements OptionSourceInterface
{
    /**#@+
     * Operator values
     */
    const LESS_THAN = 'lt';
    const EQUALS_LESS_THAN = 'lteq';
    const EQUALS = 'eq';
    const EQUALS_GREATER_THAN = 'gteq';
    const GREATER_THAN = 'gt';
    const LIKE = 'like';
    const IN = 'in';
    const IS_EQUAL_TO = 'is_equal_to';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LESS_THAN,
                'label' => __('less than')
            ],
            [
                'value' => self::EQUALS_LESS_THAN,
                'label' => __('equals or less than')
            ],
            [
                'value' => self::EQUALS,
                'label' => __('equals')
            ],
            [
                'value' => self::EQUALS_GREATER_THAN,
                'label' => __('equals or greater than')
            ],
            [
                'value' => self::GREATER_THAN,
                'label' => __('greater than')
            ],
            [
                'value' => self::LIKE,
                'label' => __('contains')
            ],
            [
                'value' => self::IN,
                'label' => __('is one of')
            ],
            [
                'value' => self::IS_EQUAL_TO,
                'label' => __('is equal to')
            ]
        ];
    }

    /**
     * Get comparison operators
     *
     * @return array
     */
    public function getComparisonOperators()
    {
        return [
            [
                'value' => self::LESS_THAN,
                'label' => __('less than')
            ],
            [
                'value' => self::EQUALS_LESS_THAN,
                'label' => __('equals or less than')
            ],
            [
                'value' => self::EQUALS,
                'label' => __('equals')
            ],
            [
                'value' => self::EQUALS_GREATER_THAN,
                'label' => __('equals or greater than')
            ],
            [
                'value' => self::GREATER_THAN,
                'label' => __('greater than')
            ],
        ];
    }

    /**
     * Get in operator
     *
     * @return array
     */
    public function getInOperator()
    {
        return [
            [
                'value' => self::IN,
                'label' => __('is one of')
            ]
        ];
    }

    /**
     * Get in extended operator
     *
     * @return array
     */
    public function getInExtendedOperator()
    {
        return [
            [
                'value' => self::IN,
                'label' => __('is one of')
            ],
            [
                'value' => self::IS_EQUAL_TO,
                'label' => __('is equal to')
            ]
        ];
    }

    /**
     * Get like operator
     *
     * @return array
     */
    public function getLikeOperator()
    {
        return [
            [
                'value' => self::LIKE,
                'label' => __('contains')
            ]
        ];
    }

    /**
     * Get equal operator
     *
     * @return array
     */
    public function getEqualOperator()
    {
        return [
            [
                'value' => self::EQUALS,
                'label' => __('equals')
            ]
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->toOptionArray();
        $result = [];
        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }
}
