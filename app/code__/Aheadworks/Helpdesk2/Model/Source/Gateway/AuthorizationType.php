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
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AuthorizationType
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class AuthorizationType implements OptionSourceInterface
{
    /**
     * Authorization types
     */
    const GOOGLE_TYPE = 'google';
    const DEFAULT_TYPE = 'default';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Default'),
                'value' => self::DEFAULT_TYPE
            ],
            [
                'label' => __('Google'),
                'value' => self::GOOGLE_TYPE
            ]
        ];
    }
}
