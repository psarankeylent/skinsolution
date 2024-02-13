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
namespace Aheadworks\Helpdesk2\Setup\Updater;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Setup\TicketEavSetup;
use Aheadworks\Helpdesk2\Setup\TicketEavSetupFactory;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Data
 *
 * @package Aheadworks\Helpdesk2\Setup\Updater
 */
class Data
{
    /**
     * @var TicketEavSetupFactory
     */
    private $ticketSetupFactory;

    /**
     * @param TicketEavSetupFactory $ticketSetupFactory
     */
    public function __construct(
        TicketEavSetupFactory $ticketSetupFactory
    ) {
        $this->ticketSetupFactory = $ticketSetupFactory;
    }

    /**
     * Upgrade to version 1.0.0
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function upgradeTo100(ModuleDataSetupInterface $setup)
    {
        $this->addIsLockedAttribute($setup);
        return $this;
    }

    /**
     * Add is locked ticket attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function addIsLockedAttribute($setup)
    {
        /** @var TicketEavSetup $ticketSetup */
        $ticketSetup = $this->ticketSetupFactory->create(['setup' => $setup]);
        $ticketSetup->addAttribute(
            TicketModel::ENTITY,
            TicketInterface::IS_LOCKED,
            ['type' => 'static']
        );

        return $this;
    }
}
