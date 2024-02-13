<?php

namespace ConsultOnly\ConsultOnlyTab\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template;

class Prescriptions extends Generic implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'tab/prescriptions.phtml';
    protected $consultOnlyFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory $consultOnlyFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_coreRegistry = $registry;
        $this->consultOnlyFactory = $consultOnlyFactory;
    }

    // Return data array of Customer Prescriptions
    public function getPrescriptionsListByCustomer($customerId)
    {
        $prescriptions = [];

        $collection = $this->consultOnlyFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);

        if(!empty($collection->getData()))
        {
            $prescriptions = $collection->getData();
        }

        return $prescriptions;
    }


    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Prescriptions');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Prescriptions');
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
        return '';
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
}

