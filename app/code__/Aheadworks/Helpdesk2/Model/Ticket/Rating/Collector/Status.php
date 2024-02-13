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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status\Factory as StatusCollectorFactory;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
class Status extends AbstractCollector
{
    /**
     * @var StatusCollectorFactory
     */
    private $statusCollectorFactory;

    /**
     * @param StatusCollectorFactory $statusCollectorFactory
     */
    public function __construct(
        StatusCollectorFactory $statusCollectorFactory
    ) {
        $this->statusCollectorFactory = $statusCollectorFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        $statusCollector = $this->statusCollectorFactory->createByStatus($this->ticket->getStatusId());
        return $statusCollector->collect($this->ticket);
    }
}
