<?php

namespace Ssmd\SalesNotes\Block\Adminhtml\OrderEdit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


class SalesNotes extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'sales_note.phtml';
    protected $orderFactory;
    protected $formKey;
    protected $collectionFactory;
    protected $adminSession;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\OrderFactory $orderFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Ssmd\SalesNotes\Model\ResourceModel\SalesNotes\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->orderFactory = $orderFactory;
        $this->formKey = $formKey;
        $this->collectionFactory = $collectionFactory;
        $this->adminSession = $adminSession;
        parent::__construct($context, $data);
    }

    public function getNotesCollection(){

        $userId = $this->getAdminUserId();
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('order_id', $this->getOrderId());
        $collection->setOrder('created_at', 'DESC');
        return $collection;
    }
    public function getAdminUserName()
    {
        //return $this->adminSession->getUser()->getUsername();
        return $this->adminSession->getUser()->getFirstname()." ".$this->adminSession->getUser()->getLastname();
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder() ? $this->getOrder()->getId() : null;
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Order Notes');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Order Notes');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getAdminUserId()
    {
        return $this->adminSession->getUser()->getUserId();
    }

}
