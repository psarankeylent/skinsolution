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
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor\Type;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\ProcessorInterface;

/**
 * Class ContactUsForm
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor\Type
 */
class ContactUsForm implements ProcessorInterface
{
    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @param CommandInterface $createCommand
     */
    public function __construct(
        CommandInterface $createCommand
    ) {
        $this->createCommand = $createCommand;
    }

    /**
     * @inheritDoc
     */
    public function process($rejectedMessage)
    {
        $ticketData = $rejectedMessage->getMessageData();
        $this->createCommand->execute($ticketData);

        return true;
    }
}
