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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;

/**
 * MessageFactory class for @see \Aheadworks\Helpdesk2\Model\Ticket\Message
 */
class MessageFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $instanceName = null;

    /**
     * @var Resolver
     */
    private $authorResolver;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Resolver $authorResolver
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Resolver $authorResolver,
        $instanceName = \Aheadworks\Helpdesk2\Model\Ticket\Message::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->authorResolver = $authorResolver;
    }

    /**
     * Create base message object
     *
     * @return Message
     */
    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }

    /**
     * Create message from Agent
     *
     * @return Message
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function createFromAgent()
    {
        $author = $this->authorResolver->resolveAgent();
        return $this->create()
            ->setType(Type::ADMIN)
            ->setAuthorName($author->getName())
            ->setAuthorEmail($author->getEmail());
    }

    /**
     * Create Internal Note message
     *
     * @return Message
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function createInternalNote()
    {
        return $this->createFromAgent()
            ->setType(Type::INTERNAL);
    }

    /**
     * Create message for update detector
     *
     * @param TicketInterface $ticket
     * @return Message
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createForDetector($ticket)
    {
        $author = $this->authorResolver->resolveForDetector($ticket);
        return $this->create()
            ->setType(Type::SYSTEM)
            ->setAuthorName($author->getName())
            ->setAuthorEmail($author->getEmail());
    }

    /**
     * Create message from Automation author
     *
     * @return Message
     */
    public function createFromAutomation()
    {
        $author = $this->authorResolver->resolveAutomation();
        return $this->create()
            ->setType(Type::SYSTEM)
            ->setAuthorName($author->getName())
            ->setAuthorEmail($author->getEmail());
    }

    /**
     * Create escalation ticket message
     *
     * @return Message
     */
    public function createEscalation()
    {
        return $this->create()
            ->setType(Type::ESCALATION)
            ->setContent(__('Ticket has been escalated'));
    }

    /**
     * Create ticket locking message
     *
     * @return Message
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createTicketLock()
    {
        return $this->createFromAgent()
            ->setType(Type::TICKET_LOCK);
    }
}
