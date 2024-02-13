<?php

namespace Backend\SyncMedical\Block\Adminhtml\CustomerEdit;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SyncMedicalButton extends GenericButton implements ButtonProviderInterface {
    
    /**
     * Button constructor.
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     *  @return array
     */
    public function getButtonData()
    {
        $customerId     = $this->getCustomerId();
        $data = [];
        if($customerId)
        {
            $data = [

                    'label' => __('Sync Medical'),
                    'class' => 'sync_medical primary',
                    //'on_click' => sprintf("window.open('%s');", $this->getMedicalUrl()),   // For opening on to other tab.
                    'on_click' => sprintf("location.href = '%s';", $this->getMedicalUrl()),  // For opening on to same tab.
                    'sort_order' => 50,
            ];
        }        
        return $data;
    }

    /**
     *  @return string
     */
    public function getMedicalUrl()
    {
        return $this->getUrl('backend/syncmedical/index', ['customer_id' => $this->getCustomerId()]);
    }
}
