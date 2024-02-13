<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton; 

class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry     
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry        
    ) {
        parent::__construct($context, $registry);        
    }

    /**
     * @return array
     */
    public function getButtonData()
    {        
        $data = [];
        
        $data = [
                'label' => __('Back'),
                'class' => 'back',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'back']],
                    'form-role' => 'back',
                ],
                'onclick' => "setLocation('".$this->getUrl('customer/index/index')."')",
                'sort_order' => 100,
        ];
        
        return $data;
    }
}
