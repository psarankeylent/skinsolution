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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\Creation;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrders;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrdersFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\Creation
 */
class Order implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CustomerOrdersFactory
     */
    private $customerOrdersSourceFactory;

    /**
     * @param ArrayManager $arrayManager
     * @param UserContextInterface $userContext
     * @param CustomerOrdersFactory $customerOrdersSourceFactory
     */
    public function __construct(
        ArrayManager $arrayManager,
        UserContextInterface $userContext,
        CustomerOrdersFactory $customerOrdersSourceFactory
    ) {
        $this->arrayManager = $arrayManager;
        $this->userContext = $userContext;
        $this->customerOrdersSourceFactory = $customerOrdersSourceFactory;
    }

    /**
     * Prepare department selector
     *
     * @param array $jsLayout
     * @param CreationRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        if ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER) {
            $customerId =$this->userContext->getUserId();
            $defaultOrderId = $renderer->getRequest()->getParam('order_id');
            $generalFieldsetPath = 'components/aw_helpdesk2_form/children/general/children';
            $jsLayout = $this->arrayManager->merge(
                $generalFieldsetPath,
                $jsLayout,
                [
                    TicketInterface::ORDER_ID => $this->getData($customerId, $defaultOrderId)
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Get order ID selector data
     *
     * @param int $customerId
     * @param int|null $defaultOrderId
     * @return array
     */
    private function getData($customerId, $defaultOrderId)
    {
        $data = [
            'component' => 'Magento_Ui/js/form/element/select',
            'dataScope' => TicketInterface::ORDER_ID,
            'provider' => 'aw_helpdesk2_form_data_provider',
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/select',
            'label' => __('Order'),
            'sortOrder' => '15',
            'options' => $this->getOptions($customerId)
        ];

        if ($defaultOrderId) {
            $data['default'] = $defaultOrderId;
        }

        return $data;
    }

    /**
     * Get options
     *
     * @param int $customerId
     * @return array
     */
    private function getOptions($customerId)
    {
        /** @var CustomerOrders $optionSource */
        $optionSource = $this->customerOrdersSourceFactory->create(['customerId' => $customerId]);

        return $optionSource->toOptionArray();
    }
}
