<?php


namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit;

/**
 * Tabs Class
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('legacy_edit');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subscriptions Information'));
    }

    protected function _prepareLayout()
    {
        $this->addTab(
            'main',
            [
                'label'  => __('Legacy Subscriptions'),
                'url'    => $this->getUrl('customer/subscription/legacySubscriptionGrid', ['_current' => true]),
                'class'  => 'ajax',
                'active' => true
            ]
        );
        $this->addTab(
            'Legacy',
            [
                'label' => __('Legacy Subscriptions Orders'),
                'url'   => $this->getUrl('customer/subscription/subscriptionGridOrders', ['_current' => true]),
                'class' => 'ajax'

               // 'content' => $this->getLayout()->getBlock(
                   // 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab\LegacySubscriptionOrders'
                //)
            ]
        );
        return parent::_prepareLayout();
    }
    
}
