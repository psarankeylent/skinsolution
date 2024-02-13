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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Gateway
 */
class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
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
     * @inheritdoc
     */
    public function prepareModelBeforeSave($model)
    {
        foreach ($this->processors as $processor) {
            $processor->prepareModelBeforeSave($model);
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function prepareModelAfterLoad($model)
    {
        foreach ($this->processors as $processor) {
            $processor->prepareModelAfterLoad($model);
        }

        return $model;
    }
}
