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
 * Class GatewayProtocol
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class GatewayProtocol implements OptionSourceInterface
{
    /**
     * Authorization types
     */
    const POP3 = 'pop3';
    const IMAP = 'imap';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('POP3'),
                'value' => self::POP3
            ],
            [
                'label' => __('IMAP'),
                'value' => self::IMAP
            ],
        ];
    }
}
