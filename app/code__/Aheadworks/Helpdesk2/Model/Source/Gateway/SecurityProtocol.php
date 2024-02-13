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
 * Class SecurityProtocol
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class SecurityProtocol implements OptionSourceInterface
{
    /**
     * Security protocol types
     */
    const NONE = 'none';
    const SSL = 'SSL';
    const TLS = 'TLS';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('None'),
                'value' => self::NONE
            ],
            [
                'label' => __('SSL'),
                'value' => self::SSL
            ],
            [
                'label' => __('TLS'),
                'value' => self::TLS
            ]
        ];
    }
}
