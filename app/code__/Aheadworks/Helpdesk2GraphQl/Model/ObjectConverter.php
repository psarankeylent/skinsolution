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
namespace Aheadworks\Helpdesk2GraphQl\Model;

use Aheadworks\Helpdesk2GraphQl\Model\DataProcessor\Pool as ProcessorsPool;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class ObjectConverter
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model
 */
class ObjectConverter
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ProcessorsPool
     */
    private $processorsPool;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ProcessorsPool $processorsPool
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        ProcessorsPool $processorsPool
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->processorsPool = $processorsPool;
    }

    /**
     * Convert object to array with processing
     *
     * @param mixed $object
     * @param string $instanceName
     * @return array
     */
    public function convertToArray($object, $instanceName)
    {
        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $object,
            $instanceName
        );
        $data['model'] = $object;

        $dataArrayProcessor = $this->processorsPool->getForInstance($instanceName);
        if ($dataArrayProcessor) {
            $data = $dataArrayProcessor->process($data);
        }

        return $data;
    }
}
