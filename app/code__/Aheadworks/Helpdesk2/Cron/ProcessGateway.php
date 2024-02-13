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
use Aheadworks\Helpdesk2\Api\DepartmentManagementInterface;
use Aheadworks\Helpdesk2\Model\Flag;

/**
 * Class ProcessGateway
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class ProcessGateway
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
     * @var DepartmentManagementInterface
     */
    private $departmentManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param DepartmentManagementInterface $departmentManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        DepartmentManagementInterface $departmentManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->departmentManagement = $departmentManagement;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_PROCESS_GATEWAY_LAST_EXEC_TIME, 300)) {
            try {
                $this->departmentManagement->processGateways();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_PROCESS_GATEWAY_LAST_EXEC_TIME);
        }
    }
}
