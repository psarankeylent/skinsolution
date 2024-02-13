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
 * Interface DataProcessorInterface
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\DataProcessor
 */
Interface DataProcessorInterface
{
    /**
     * Process data array before send
     *
     * @param array $data
     * @return array
     */
    public function process(array $data): array;
}
