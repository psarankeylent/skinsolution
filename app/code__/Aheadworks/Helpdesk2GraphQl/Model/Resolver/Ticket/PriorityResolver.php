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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as Source;
use Aheadworks\Helpdesk2GraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class PriorityResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Ticket
 */
class PriorityResolver extends AbstractResolver
{
    /**
     * @var Source
     */
    private $source;

    /**
     * @param Source $source
     */
    public function __construct(
        Source $source
    ) {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /** @var TicketInterface $ticket */
        $ticket = $value['model'];

        $option = $this->source->getOptionById($ticket->getPriorityId());

        return [
            'id' => $option['value'],
            'label' => $option['label'],
        ];
    }
}
