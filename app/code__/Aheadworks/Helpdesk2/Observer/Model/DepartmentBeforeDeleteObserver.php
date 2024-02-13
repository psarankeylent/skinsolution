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
namespace Aheadworks\Helpdesk2\Observer\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department\TicketChecker;
use Aheadworks\Helpdesk2\Model\Department\ConfigChecker;

/**
 * Class DepartmentBeforeDeleteObserver
 *
 * @package Aheadworks\Helpdesk2\Observer\Model
 */
class DepartmentBeforeDeleteObserver implements ObserverInterface
{
    /**
     * @var TicketChecker
     */
    private $ticketChecker;

    /**
     * @var ConfigChecker
     */
    private $configChecker;

    /**
     * @param TicketChecker $ticketChecker
     * @param ConfigChecker $configChecker
     */
    public function __construct(
        TicketChecker $ticketChecker,
        ConfigChecker $configChecker
    ) {
        $this->ticketChecker = $ticketChecker;
        $this->configChecker = $configChecker;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var DepartmentInterface $department */
        $department = $observer->getDataObject();
        if ($this->ticketChecker->hasTicketsAssigned($department->getId())) {
            throw new LocalizedException(
                __(
                    'Department "%1" has tickets assigned to it and cannot be deleted',
                    $department->getName()
                )
            );
        }

        if ($this->configChecker->isSetAsPrimary($department->getId())) {
            throw new LocalizedException(
                __(
                    'Department "%1" set as primary in the extension settings and cannot be deleted',
                    $department->getName()
                )
            );
        }
    }
}
