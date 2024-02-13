<?php

namespace Ssmd\Impersonation\Block\Adminhtml\Order\Create;

class CustomerPlugin extends \Magento\Sales\Block\Adminhtml\Order\Create\Customer
{

    protected $dataHelper;

    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Ssmd\Impersonation\Helper\Data $dataHelper
       
    ) {
        $this->sessionManager = $sessionManager;
        $this->dataHelper = $dataHelper;
        
    }

    public function afterGetButtonsHtml(\Magento\Sales\Block\Adminhtml\Order\Create\Customer $subject, $result)
    { 
     
        if ($subject->_authorization->isAllowed('Magento_Customer::manage')) {


            if($this->dataHelper->isModuleEnabled())
            {
                return '';
            }
            else
            {
                $addButtonData = [
                    'label' => __('Create New Customer'),
                    'onclick' => 'order.setCustomerId(false)',
                    'class' => 'primary',
                    ];
                    return $subject->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
                        ->setData($addButtonData)
                        ->toHtml();

            }
            
        }

            return '';

    }
}
