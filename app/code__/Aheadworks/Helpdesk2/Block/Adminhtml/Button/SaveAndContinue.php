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
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class SaveAndContinue
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
class SaveAndContinue extends AbstractButton
{
    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @param Context $context
     * @param int $sortOrder
     */
    public function __construct(
        Context $context,
        int $sortOrder = 30
    ) {
        parent::__construct($context);
        $this->sortOrder = $sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => $this->sortOrder,
        ];
    }
}
