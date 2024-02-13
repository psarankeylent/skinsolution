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
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Form\Button;

use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Form\Button
 */
class Delete extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $gatewayId = $this->context->getRequest()->getParam(GatewayDataInterface::ID);
        if ($gatewayId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', [GatewayDataInterface::ID => $gatewayId]) . '\')',
                'sort_order' => 15,
            ];
        }

        return $data;
    }
}
