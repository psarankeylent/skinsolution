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
namespace Aheadworks\Helpdesk2\Block\Customer\Order\Info\Buttons;

use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Magento\Customer\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

/**
 * Class CreateTicketButton
 *
 * @package Aheadworks\Helpdesk2\Block\Customer\Order\Info\Buttons
 */
class CreateTicketButton extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Helpdesk2::customer/order/info/buttons/create_ticket.phtml';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param UrlBuilder $urlBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        UrlBuilder $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->httpContext = $httpContext;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve current order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Check if show submit button
     *
     * @return bool
     */
    public function isShowButton()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * Get url for submit ticket action
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        $order = $this->getOrder();

        return $this->urlBuilder->getTicketCreateLink(['order_id' => $order->getEntityId()]);
    }
}
