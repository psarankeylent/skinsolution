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
namespace Aheadworks\Helpdesk2\Model\Source\RejectedMessage;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\RejectedMessage
 */
class Type implements OptionSourceInterface
{
    /**
     * Rejected message type values
     */
    const EMAIL = 'email';
    const CONTACT_US_FORM = 'contact_us_form';

    /**
     * @var array
     */
    private $options;

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = [
                ['value' => self::EMAIL,  'label' => __('Email')],
                ['value' => self::CONTACT_US_FORM,  'label' => __('Contact Us form')],
            ];;
        }

        return $this->options;
    }
}
