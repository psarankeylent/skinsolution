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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\RejectingPatterns;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\RejectingPattern\Scope as ScopeSource;

/**
 * Class ScopeDataResolver
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\RejectingPatterns
 */
class ScopeDataResolver
{
    /**
     * @var array
     */
    private $scopeMap = [
        ScopeSource::SUBJECT => TicketInterface::SUBJECT,
        ScopeSource::BODY => MessageInterface::CONTENT,
    ];

    /**
     * @param array $scopeMap
     */
    public function __construct(array $scopeMap = [])
    {
        $this->scopeMap = array_merge($this->scopeMap, $scopeMap);
    }

    /**
     * Check if isset data by scope type
     *
     * @param array $ticketData
     * @param string $scopeType
     * @return bool
     */
    public function hasData($ticketData, $scopeType)
    {
        return array_key_exists($scopeType, $this->scopeMap)
            && array_key_exists($this->scopeMap[$scopeType], $ticketData);
    }

    /**
     * Retrieve data from ticket by scope
     *
     * @param array $ticketData
     * @param string $scopeType
     * @return string|null
     */
    public function getData($ticketData, $scopeType)
    {
        return $ticketData[$this->scopeMap[$scopeType]] ?? null;
    }
}

