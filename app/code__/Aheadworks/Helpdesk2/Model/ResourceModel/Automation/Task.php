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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Automation;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;

/**
 * Class Task
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Automation
 */
class Task extends AbstractDb
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_automation_task';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TaskInterface::ID);
    }
}
