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
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class RejectingPattern
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email
 */
class RejectingPattern extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_email_rejecting_pattern';
    const SCOPE_TABLE = 'aw_helpdesk2_email_rejecting_pattern_scope';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RejectingPatternInterface::ID);
    }
}
