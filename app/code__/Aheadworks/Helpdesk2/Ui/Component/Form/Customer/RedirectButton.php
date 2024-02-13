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
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Customer;

use Magento\Ui\Component\Container;

/**
 * Class RedirectButton
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Customer
 */
class RedirectButton extends Container
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $requestParam = $this->context->getRequestParam($config['requestParamName']);
        $config['urlToRedirect'] = $this->context->getUrl(
            $config['pathToRedirect'],
            [$config['paramName'] => $requestParam]
        );
        $this->setData('config', $config);

        parent::prepare();
    }
}
