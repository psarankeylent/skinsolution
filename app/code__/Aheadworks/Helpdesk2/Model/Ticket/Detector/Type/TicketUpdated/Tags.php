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
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated;

use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TagsSource;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Tags
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated
 */
class Tags implements DetectorInterface
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
     * @var TagsSource
     */
    private $source;

    /**
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     * @param TagsSource $statusSource
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory,
        TagsSource $statusSource
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->source = $statusSource;
    }

    /**
     * @inheritdoc
     *
     * @param array $data
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function detect($data)
    {
        /** @var TicketInterface $oldTicket */
        $oldTicket = $data['old_ticket'];
        /** @var TicketInterface $newTicket */
        $newTicket = $data['new_ticket'];

        if ($oldTicket->getTagNames() != $newTicket->getTagNames()) {
            $message = $this->messageFactory->createForDetector($newTicket);
            $message
                ->setTicketId($newTicket->getEntityId())
                ->setContent($this->getContent($oldTicket, $newTicket));
            $this->messageRepository->save($message);
        }
    }

    /**
     * Create message content
     *
     * @param TicketInterface $oldTicket
     * @param TicketInterface $newTicket
     * @return \Magento\Framework\Phrase
     */
    private function getContent($oldTicket, $newTicket)
    {
        $from = implode(', ', $oldTicket->getTagNames());
        $to = implode(', ', $newTicket->getTagNames());

        return $from
            ? __('%1 > <b>%2</b>', [$from, $to])
            : __('<b>%1</b>', [$to]);
    }
}
