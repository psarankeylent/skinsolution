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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\CustomerRating\CanRateChecker;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Rate
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Rate implements CommandInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var CanRateChecker
     */
    private $checker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param TicketManagementInterface $ticketManagement
     * @param CanRateChecker $canRateChecker
     * @param Config $config
     */
    public function __construct(
        TicketManagementInterface $ticketManagement,
        CanRateChecker $canRateChecker,
        Config $config
    ) {
        $this->ticketManagement = $ticketManagement;
        $this->checker = $canRateChecker;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['ticket'])) {
            throw new \InvalidArgumentException('Ticket is required');
        }
        if (!isset($data['rating'])) {
            throw new \InvalidArgumentException('Rating is required');
        }
        if (($data['rating']) == 0) {
            throw new LocalizedException(__('Invalid Customer Rating value'));
        }

        if (!$this->config->isEnabledCustomerRating()) {
            throw new LocalizedException(__('Ticket rating disabled.'));
        }

        /** @var TicketInterface $ticket */
        $ticket = $data['ticket'];

        if (!$this->checker->canRate($ticket)) {
            throw new LocalizedException(__('Rating time has expired.'));
        }

        $ticket->setCustomerRating($data['rating']);
        $this->ticketManagement->updateTicket($ticket);

        return $ticket;
    }
}
