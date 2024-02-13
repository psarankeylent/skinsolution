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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Model\DataProcessor;

/**
 * Class PostDataComposite
 *
 * @package Aheadworks\Bup\Model\DataProcessor
 */
class PostDataComposite implements PostDataProcessorInterface
{
    /**
     * @var PostDataProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof PostDataProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Processor instance %s does not implement required interface.',
                        PostDataProcessorInterface::class
                    )
                );
            }
            $data = $processor->prepareEntityData($data);
        }

        return $data;
    }
}
