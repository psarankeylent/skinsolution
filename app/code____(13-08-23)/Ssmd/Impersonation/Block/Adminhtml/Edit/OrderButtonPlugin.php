<?php

namespace Ssmd\Impersonation\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class OrderButtonPlugin
{

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;
    protected $urlInterface;
    protected $customerFactory;
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->authorization = $context->getAuthorization();
        $this->urlInterface = $urlInterface;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
    }

    public function afterGetButtonData(\Magento\Customer\Block\Adminhtml\Edit\OrderButton $subject, $result)
    {
        $customerId = $subject->getCustomerId();
        $data = [];
        if ($customerId && $this->authorization->isAllowed('Magento_Sales::create')) {
            $data = [
                'label' => __('Start Impersonation'),
		'on_click' => sprintf("location.href = '%s';", $this->getImpersonationUrl($subject->getCustomerId())),
                'class' => 'add',
                'sort_order' => 40,
            ];
        }
        return $data;

    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getImpersonationUrl($customerId)
    {
        return $this->urlInterface->getUrl('impersonation/index/view'.'/id/'.$customerId);
    }
}

