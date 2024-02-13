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
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Modifier\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Modifier\ModifierInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class CustomerId
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Modifier\Ticket
 */
class CustomerId implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function modifyArgs(Field $field, $context, ResolveInfo $info, $args)
    {
        $args['filter'][TicketInterface::CUSTOMER_ID]['eq'] = $context->getUserId();

        return $args;
    }
}
