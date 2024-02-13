<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


/**
 * Customer Legacy Subscriptions Orders In Grid 
 */
class LegacySubscriptionOrders extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry; 
    /**
     * @var \LegacySubscription\Subscriptions\Model\ResourceModel\Legacy\CollectionFactory
     */
    protected $collectionFactory;
    protected $orderFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \LegacySubscription\Subscriptions\Model\ResourceModel\Legacy\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \LegacySubscription\Subscriptions\Model\ResourceModel\Legacy\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->orderFactory = $orderFactory;
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('legacysubscriptionOrders');
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
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'index' => 'id'
            ]
        );

        $this->addColumn(
            'legacy_subscription',
            [
                'header' => __('Legacy Subscription'),
                'index' => 'legacy_subscription',
                'width' => '80px'
            ]
        );

        $this->addColumn(
            'order_increment_id',
            [
                'header' => __('Mage Order ID'),
                'index' => 'order_increment_id',
                'width' => '50px',
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
        return __('Legacy Subscriptions Orders');
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Legacy Subscriptions Orders');
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
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
    

    public function getRowUrl($row)
    {

        $mageIncrementId = $row->getData('order_increment_id');

        $order = $this->orderFactory->create()->loadByIncrementId($mageIncrementId);
        $id = $order->getId();
        if (isset($id) && $id != "") {
            return $this->getUrl(
                'sales/order/view',
                ['order_id' => $id]
            );
        } else {
            return parent::getRowUrl($row);
        }

        

        return $this->getUrl('sales/order/view', ['params' => $params]);
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
