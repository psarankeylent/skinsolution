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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Automation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action as ActionSource;
use Aheadworks\Helpdesk2\Model\Automation\ValueMapper;

/**
 * Class Actions
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Automation
 */
class Actions implements ProcessorInterface
{
    /**
     * @var ActionSource
     */
    private $actionSource;

    /**
     * @var ValueMapper
     */
    private $valueMapper;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ActionSource $actionSource
     * @param ValueMapper $valueMapper
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ActionSource $actionSource,
        ValueMapper $valueMapper,
        ArrayManager $arrayManager
    ) {
        $this->actionSource = $actionSource;
        $this->valueMapper = $valueMapper;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareMetaData($meta)
    {
        $meta = $this->arrayManager->set(
            'aw_helpdesk2_automation_form_data_source/arguments/data/js_config/value_mapper/actions',
            $meta,
            [
                'action_options' => $this->actionSource->getAvailableOptionsByEventType(),
                'value_options' => $this->valueMapper->getAvailableActionValuesByActionType(),
                'config_types' => $this->actionSource->getAvailableConfigTypesByEventType(),
                'config_options' => $this->valueMapper->getAvailableActionConfigOptionsByConfigType()
            ]
        );

        return $meta;
    }
}
