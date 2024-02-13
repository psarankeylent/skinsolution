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
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Message\Generator;

use Aheadworks\Helpdesk2\Model\Source\Automation\Action as ActionSource;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event as EventSource;

/**
 * Class Content
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Content\Generator
 */
class Content
{
    /**
     * @var EventSource
     */
    private $eventSource;

    /**
     * @param EventSource $eventSource
     */
    public function __construct(EventSource $eventSource)
    {
        $this->eventSource = $eventSource;
    }

    /**
     * Create message content
     *
     * @param string $eventId
     * @param string $from
     * @param string $to
     * @return \Magento\Framework\Phrase
     */
    public function getContent($eventId, $from, $to)
    {
        $eventName = $this->eventSource->getOptionById($eventId)['label'];
        return $from
            ? __(
                '<p>%1 > <b>%2</b></p><p><small>Event: %3</small></p>',
                [$from, $to, $eventName]
            )
            : __(
                '<p><b>%1</b></p><p><small>Event: %2</small></p>',
                [$to, $eventName]
            );
    }
}
