<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


/**
 * Customer M1 Subscriptions Grid 
 */
class SubscriptionGrid extends \Magento\Backend\Block\Widget\Grid\Extended implements TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry; 
    /**
     * @var \LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
    }
    
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Legacy Subscriptions');
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Legacy Subscriptions');
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

	/**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
    	//replace the tab with the url you want
        return $this->getUrl('customer/subscription/subscriptiongrid', ['_current' => true]);
    }
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
    

    
}
