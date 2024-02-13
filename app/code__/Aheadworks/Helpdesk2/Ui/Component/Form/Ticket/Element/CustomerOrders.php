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
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class CustomerOrders
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element
 */
class CustomerOrders extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ContextInterface $context
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->authorization->isAllowed('Magento_Sales::sales_order')) {
            $config = $this->getData('config');
            $config['disabled'] = true;
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
