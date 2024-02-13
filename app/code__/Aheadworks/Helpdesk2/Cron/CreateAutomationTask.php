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
namespace Aheadworks\Helpdesk2\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Flag;
use Aheadworks\Helpdesk2\Model\Automation\Event\RecurringTaskHandler;

/**
 * Class CreateAutomationTask
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class CreateAutomationTask
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var RecurringTaskHandler
     */
    private $recurringTaskHandler;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param RecurringTaskHandler $recurringTaskHandler
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        RecurringTaskHandler $recurringTaskHandler
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->recurringTaskHandler = $recurringTaskHandler;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_CREATE_AUTOMATION_EXEC_TIME)) {
            try {
                $this->recurringTaskHandler->trigger();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_CREATE_AUTOMATION_EXEC_TIME);
        }
    }
}
