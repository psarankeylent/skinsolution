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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $objectClasses;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $objectClasses
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $objectClasses
    ) {
        $this->objectManager = $objectManager;
        $this->objectClasses = $objectClasses;
    }

    /**
     * Create status collector by status ID
     *
     * @param int $statusId
     * @return AbstractCollector
     */
    public function createByStatus($statusId)
    {
        if (isset($this->objectClasses[$statusId])) {
            $type = $this->objectClasses[$statusId];
        } else {
            $type = $this->objectClasses['default'];
        }

        $instance = $this->objectManager->create($type);
        if (!$instance instanceof AbstractCollector) {
            throw new \InvalidArgumentException(
                sprintf('Collector instance does not extend %s.', AbstractCollector::class)
            );
        }

        return $instance;
    }
}
