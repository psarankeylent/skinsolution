<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


/**
 * Customer M1 Subscriptions
 */
class Subscriptions extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('subscriptionGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        //echo "<pre>"; print_r($collection->getData()); exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'profile_id',
            [
                'header' => __('ID'),
                'index' => 'profile_id'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Description'),
                'index' => 'title',
                'width' => '80px',
                'renderer' => 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer\Description'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Subscription Status'),
                'index' => 'status',
                'width' => '50px',
                'renderer' => 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer\Status'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'amount',
            [
                'header' => __('Subtotal'),
                'type' => 'float',
                'index' => 'amount',
                'width' => '50px',
                'renderer' => 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer\Subtotal'
            ]
        );
        $this->addColumn(
            'create_date',
            [
                'header' => __('Purchased'),
                'index' => 'create_date',
                'width' => '50px',
                'renderer' => 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer\CreatedDate'
            ]
        );

        return parent::_prepareColumns();
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
        if ($this->getId()) {
            return true;
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getId()) {
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
        return $this->getUrl('customer/subscription/subscriptiongrid', ['_current' => true]);
    }
    
    /**
     * Retrieve the Url for a specified row.
     *
     * @param \LegacySubscription\Subscriptions\Model\CustomerSubscription\Api\Data\SubscriptionInterface $row
     * @return string
     */

    public function getRowUrl($row)
    {
        // Save customer id in session
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coreSession = $objectManager->get('Magento\Framework\Session\SessionManagerInterface');

        $coreSession->start();
        $coreSession->setData('mage_customer_id',$row->getData('customer_id'));


        /** @var \LegacySubscription\Subscriptions\Model\CustomerSubscription $row */

        return $this->getUrl('customer/subscription/subscriptionView', ['id' => $row->getData('id')]);

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
