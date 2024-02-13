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
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Message;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Generator\Content as ContentGenerator;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Management
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Message
 */
class Management
{
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var ContentGenerator
     */
    private $contentGenerator;

    /**
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     * @param ContentGenerator $contentGenerator
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory,
        ContentGenerator $contentGenerator
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->contentGenerator = $contentGenerator;
    }

    /**
     * Create system message with Automation author
     *
     * @param int $ticketId
     * @param string $prevValue
     * @param string $newValue
     * @param string $eventId
     * @return MessageInterface
     * @throws CouldNotSaveException
     */
    public function createAutomationMessage($ticketId, $prevValue, $newValue, $eventId)
    {
        $content = $this->contentGenerator->getContent($eventId, $prevValue, $newValue);
        $message = $this->messageFactory->createFromAutomation();
        $message
            ->setTicketId($ticketId)
            ->setContent($content);

        return $this->messageRepository->save($message);
    }
}
