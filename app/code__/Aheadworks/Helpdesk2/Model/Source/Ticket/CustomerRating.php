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
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerRating
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class CustomerRating implements OptionSourceInterface
{
    /**#@+
     * Constants defined for rating values calculation
     */
    const STARS_COUNT = 5;
    const MAX_VALUE = 5;
    const MIN_VALUE = 1;
    /**#@-*/

    /**
     * @var array
     */
    private $options;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            for($i = 1; $i <= self::STARS_COUNT; $i++) {
                $this->options[] = [
                    'value' => $i,
                    'label' => __('%1 star', $i)
                ];
            }
        }

        return $this->options;
    }
}
