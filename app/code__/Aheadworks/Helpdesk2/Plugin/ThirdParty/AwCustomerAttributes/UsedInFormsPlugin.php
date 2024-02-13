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
namespace Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes;

/**
 * Class UsedInFormsPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes
 */
class UsedInFormsPlugin
{
    const ADMIN_TICKET_VIEW = 'aw_helpdesk2_adminhtml_ticket';

    /**
     * Add help desk admin ticket option
     *
     * @param \Aheadworks\CustomerAttributes\Model\Source\Attribute\UsedInForms $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToOptionArray($subject, $result)
    {
        $result[] = [
            'label' => __('Admin Ticket View'),
            'value' => self::ADMIN_TICKET_VIEW
        ];

        return $result;
    }
}
