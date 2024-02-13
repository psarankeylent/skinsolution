<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

/**
 * Class SaveAndContinueButton
 */
class SaveAndContinueButton extends GenericButton  implements ButtonProviderInterface
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
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 90,
        ];
       
        return $data;
    }
}
