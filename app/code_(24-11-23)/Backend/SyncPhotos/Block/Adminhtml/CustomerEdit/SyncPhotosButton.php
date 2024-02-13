<?php

namespace Backend\SyncPhotos\Block\Adminhtml\CustomerEdit;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SyncPhotosButton extends GenericButton implements ButtonProviderInterface {
    
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

                    'label' => __('Sync Photos'),
                    'class' => 'sync_photos primary',
                    'on_click' => sprintf("location.href = '%s';", $this->getPhotosUrl()),
                    'sort_order' => 55,
            ];
        }        
        return $data;
    }

    /**
     *  @return string
     */
    public function getPhotosUrl()
    {
        return $this->getUrl('backend/syncphotos/index', ['customer_id' => $this->getCustomerId()]);
    }
}
