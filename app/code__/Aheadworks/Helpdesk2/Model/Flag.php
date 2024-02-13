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
namespace Aheadworks\Helpdesk2\Model;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Flag extends FrameworkFlag
{
    const AW_HELPDESK2_PROCESS_GATEWAY_LAST_EXEC_TIME = 'aw_helpesk2_process_gateway_last_exec_time';
    const AW_HELPDESK2_PROCESS_EMAIL_LAST_EXEC_TIME = 'aw_helpesk2_process_email_last_exec_time';
    const AW_HELPDESK2_CREATE_AUTOMATION_EXEC_TIME = 'aw_helpesk2_create_automation_last_exec_time';
    const AW_HELPDESK2_RUN_AUTOMATION_EXEC_TIME = 'aw_helpesk2_run_automation_last_exec_time';
    const AW_HELPDESK2_FINISH_AUTOMATION_EXEC_TIME = 'aw_helpesk2_finish_automation_last_exec_time';
    const AW_HELPDESK2_UPDATE_TICKET_RATING_EXEC_TIME = 'aw_helpesk2_update_ticket_rating_last_exec_time';

    /**
     * Set flag code
     *
     * @param string $code
     * @return $this
     */
    public function setFlag($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
