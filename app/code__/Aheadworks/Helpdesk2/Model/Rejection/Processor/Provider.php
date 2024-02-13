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
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Provider
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor
 */
class Provider
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Retrieve processor by type
     *
     * @param string $type
     * @return ProcessorInterface|null
     * @throws LocalizedException
     */
    public function getProcessor($type)
    {
        if (!array_key_exists($type, $this->processors)) {
            throw new LocalizedException(__('Unknown processor type: %1', $type));
        }
        return $this->processors[$type];
    }
}
