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
namespace Aheadworks\Helpdesk2\Model\Gateway;

use Aheadworks\Helpdesk2\Model\Gateway\Processor\ProcessorInterface;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway
 */
class ProcessorPool
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * Get processor for gateway processing
     *
     * @param string $gatewayType
     * @return ProcessorInterface
     */
    public function getProcessor($gatewayType)
    {
        if (!isset($this->processors[$gatewayType])) {
            throw new \InvalidArgumentException(
                sprintf('Gateway type %s is not supported.', $gatewayType)
            );
        }

        $processor = $this->processors[$gatewayType];
        if (!$processor instanceof ProcessorInterface) {
            throw new \InvalidArgumentException(
                sprintf('Processor instance %s does not implement required interface.', ProcessorInterface::class)
            );
        }

        return $processor;
    }
}
