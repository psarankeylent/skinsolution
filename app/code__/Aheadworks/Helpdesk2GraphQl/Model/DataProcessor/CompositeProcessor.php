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
namespace Aheadworks\Helpdesk2GraphQl\Model\DataProcessor;

/**
 * Class CompositeProcessor
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\DataProcessor
 */
class CompositeProcessor implements DataProcessorInterface
{
    /**
     * @var DataProcessorInterface[]
     */
    private $processors;

    /**
     * @param DataProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function process(array $data): array
    {
        foreach ($this->processors as $processor) {
            $data = array_merge($data, $processor->process($data));
        }

        return $data;
    }
}
