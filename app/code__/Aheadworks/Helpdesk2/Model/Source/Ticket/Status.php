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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Status\Provider as StatusProvider;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class Status implements OptionSourceInterface
{
    /**
     * Ticket status values
     */
    const NEW = 1;
    const OPEN = 2;
    const WAITING = 3;
    const CLOSED = 4;

    /**
     * @var StatusProvider
     */
    private $statusProvider;

    /**
     * @var array
     */
    private $options;

    /**
     * @param StatusProvider $statusProvider
     */
    public function __construct(
        StatusProvider $statusProvider
    ) {
        $this->statusProvider = $statusProvider;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = $this->statusProvider->getListAsOptions();
        }

        return $this->options;
    }

    /**
     * Retrieve option by option id
     *
     * @param $optionId
     * @return array|null
     * @throws LocalizedException
     */
    public function getOptionById($optionId)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $optionId) {
                return $option;
            }
        }

        return null;
    }
}
