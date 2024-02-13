<?php

namespace Backend\OrderMedical\Block\Adminhtml\OrderEdit\Tab;

/**
 * Order custom tab
 *
 */
class MedicalHistory extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    protected $_template = 'tab/medical_history.phtml';

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MH\Tables\Model\MhByOrdersFactory $mhByOrdersFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory,
        array $data = []
    ) {
        $this->mhByOrdersFactory   = $mhByOrdersFactory;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getOrderMedicalHistory()
    {
        $incrementId = $this->getOrderIncrementId();
        //$medicalCollection = $this->mhByOrdersFactory->create()->getCollection();
        $medicalCollection->addFieldToFilter('increment_id', $incrementId);
        return $medicalCollection->getData();
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
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
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
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Medical History');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Medical History');
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
}