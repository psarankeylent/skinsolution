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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting
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
     * Create waiting collector by message type
     *
     * @param string $messageType
     * @return AbstractCollector
     */
    public function createByMessageType($messageType)
    {
        if (isset($this->objectClasses[$messageType])) {
            $type = $this->objectClasses[$messageType];
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
