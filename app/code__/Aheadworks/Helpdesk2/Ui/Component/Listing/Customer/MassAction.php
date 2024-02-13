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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Customer;

use Magento\Ui\Component\MassAction as UiMassAction;

/**
 * Class MassAction
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Customer
 */
class MassAction extends UiMassAction
{
    /**
     * @inheritDoc
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        foreach ($config['actions'] as &$action) {
            if ($action['type'] == 'delete') {
                $action['url'] = $this->resolveUrl();
            }
        }

        $this->setData('config', $config);
    }

    /**
     * Resolve action url
     */
    private function resolveUrl()
    {
        $orderId = $this->context->getRequestParam('order_id');
        $params = ['redirect-to-customer' => 1];
        if ($orderId) {
            $params = ['redirect-to-order' => 1];
        }

        return $this->context->getUrl('aw_helpdesk2/ticket/massDelete', $params);
    }
}
