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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Tag resource model
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Tag extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_tag';
    const RELATION_TABLE_NAME = 'aw_helpdesk2_ticket_tag';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, 'id');
    }
}
