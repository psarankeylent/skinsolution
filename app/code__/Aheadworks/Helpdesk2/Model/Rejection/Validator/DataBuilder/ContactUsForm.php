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
namespace Aheadworks\Helpdesk2\Model\Rejection\Validator\DataBuilder;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\RejectingPattern\Scope as ScopeSource;
use Magento\Framework\DataObjectFactory;

/**
 * Class ContactUsForm
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Validator\ValidationDataBuilder
 */
class ContactUsForm
{
    /**
     * @var array
     */
    private $map = [
        ScopeSource::SUBJECT => TicketInterface::SUBJECT,
        ScopeSource::BODY => MessageInterface::CONTENT,
    ];

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param array $map
     */
    public function __construct(DataObjectFactory $dataObjectFactory, array $map = [])
    {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->map = array_merge($this->map, $map);
    }

    /**
     * Build DataObject instance for next validation
     *
     * @param array $ticketData
     * @return \Magento\Framework\DataObject
     */
    public function build($ticketData)
    {
        $data = [];
        foreach ($this->map as $scopeType => $tickedDataField) {
            if (array_key_exists($tickedDataField, $ticketData)) {
                $data[$scopeType] = $ticketData[$tickedDataField];
            }
        }

        return $this->dataObjectFactory->create(['data' => $data]);
    }
}
