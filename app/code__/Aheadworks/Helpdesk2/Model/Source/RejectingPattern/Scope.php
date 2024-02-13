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
namespace Aheadworks\Helpdesk2\Model\Source\RejectingPattern;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Scope
 *
 * @package Aheadworks\Helpdesk2\Model\Source\RejectingPattern
 */
class Scope implements OptionSourceInterface
{
    /**
     * Scope types
     */
    const HEADERS = 'headers';
    const SUBJECT = 'subject';
    const BODY = 'body';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Headers'),
                'value' => self::HEADERS
            ],
            [
                'label' => __('Subject'),
                'value' => self::SUBJECT
            ],
            [
                'label' => __('Body'),
                'value' => self::BODY
            ]
        ];
    }
}
