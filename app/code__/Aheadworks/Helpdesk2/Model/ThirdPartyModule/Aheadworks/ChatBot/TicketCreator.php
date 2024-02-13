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
namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\ChatBot;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TicketCreator
 * @package Aheadworks\ChatBot\Model\ThirdPartyModule\Aheadworks\Helpdesk2
 */
class TicketCreator
{
    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param CommandInterface $createCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        CommandInterface $createCommand,
        ProcessorInterface $postDataProcessor
    ) {
        $this->createCommand = $createCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Create ticket
     *
     * @param array $data
     * @return TicketInterface|null
     */
    public function create($data)
    {
        if (!isset($data[TicketInterface::CUSTOMER_NAME])) {
            $data[TicketInterface::CUSTOMER_NAME] = 'Guest';
        }
        $data = $this->postDataProcessor->prepareEntityData($data);

        try {
            /** @var TicketInterface $ticket */
            $ticket = $this->createCommand->execute($data);
        } catch (LocalizedException $exception) {
            $ticket = null;
        }

        return $ticket;
    }
}
