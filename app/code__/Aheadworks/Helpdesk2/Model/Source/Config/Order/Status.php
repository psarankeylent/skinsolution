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
namespace Aheadworks\Helpdesk2\Model\Source\Config\Order;

use Magento\Sales\Ui\Component\Listing\Column\Status\Options as OriginalStatusSource;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Config\Order
 */
class Status extends OriginalStatusSource
{
    const ALL = 'all';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $allOption = [
            'value' => self::ALL,
            'label' => __('All Statuses')
        ];

        $optionArray = parent::toOptionArray();
        array_unshift($optionArray, $allOption);

        return $optionArray;
    }
}
