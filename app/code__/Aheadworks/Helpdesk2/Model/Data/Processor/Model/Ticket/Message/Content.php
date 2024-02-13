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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageTypeSource;
use Magento\Framework\Escaper;

/**
 * Class Content
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message
 */
class Content implements ProcessorInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var string[]
     */
    private $patternsToRemove = [
        'script_tag' => '/<script\b[^>]*>(.*?)<\/script>/is',
        'tag_events' => '/ on\w+=("[^"]*"|\'[^\']*\')/is',
        'href_js' => '/ href=("javascript:[^"]*"|\'javascript:[^\']*\')/is'
    ];

    /**
     * @param Escaper $escaper
     * @param array $patternsToRemove
     */
    public function __construct(
        Escaper $escaper,
        $patternsToRemove = []
    ) {
        $this->escaper = $escaper;
        $this->patternsToRemove = array_merge(
            $this->patternsToRemove,
            $patternsToRemove
        );
    }

    /**
     * Prepare model before save
     *
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function prepareModelBeforeSave($message)
    {
        $skipEscapeType = [
            MessageTypeSource::ADMIN,
            MessageTypeSource::SYSTEM
        ];
        if (!in_array($message->getType(), $skipEscapeType)) {
            $content = $message->getContent();
            foreach ($this->patternsToRemove as $pattern) {
                $content = preg_replace($pattern, '', $content);
            }
            $message->setContent($content);
        }

        return $message;
    }

    /**
     * Prepare model after load
     *
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function prepareModelAfterLoad($message)
    {
        return $message;
    }
}
