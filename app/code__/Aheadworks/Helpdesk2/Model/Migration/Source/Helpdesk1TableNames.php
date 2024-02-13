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
namespace Aheadworks\Helpdesk2\Model\Migration\Source;

/**
 * Class FirstHelpdeskTables
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Source
 */
class Helpdesk1TableNames
{
    /**#@+
     * Constants defined for names of the hdu1 table names.
     */
    const DEPARTMENT = 'aw_helpdesk_department';
    const DEPARTMENT_LABEL = 'aw_helpdesk_department_label';
    const DEPARTMENT_PERMISSION = 'aw_helpdesk_department_permission';
    const DEPARTMENT_GATEWAY = 'aw_helpdesk_department_gateway';
    const DEPARTMENT_GATEWAY_AUTH = 'aw_helpdesk_department_gateway_auth';
    const DEPARTMENT_GATEWAY_EMAIL = 'aw_helpdesk_gateway_mail';
    const QUICK_RESPONSE = 'aw_helpdesk_quick_response';
    const QUICK_RESPONSE_TEXT = 'aw_helpdesk_quick_response_text';
    const TICKET = 'aw_helpdesk_ticket';
    const TICKET_MESSAGE = 'aw_helpdesk_ticket_message';
    const ATTACHMENT = 'aw_helpdesk_attachment';
    /**#@-*/
}
