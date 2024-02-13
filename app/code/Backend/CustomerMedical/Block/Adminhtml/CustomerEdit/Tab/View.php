<?php

namespace Backend\CustomerMedical\Block\Adminhtml\CustomerEdit\Tab;

class View extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    protected $scoreCredit;
    protected $_storeManager;
    protected $storecreditFactory;
    protected $storecreditCustomerBalanceFactory;
    protected $formKey;
    /**
     * Template
     *
     * @var string
     */
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->_coreRegistry        = $registry;
        $this->_storeManager        = $storeManager;
        $this->customerRepository   = $customerRepository;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->medicalFactory       = $medicalFactory;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }


    public function getCustomerMedicalHistory()
    {
        $customerId     = $this->getCustomerId();
        $medicalCollection = $this->medicalFactory->create()->getCollection();
        $medicalCollection->addFieldToFilter('customer_id', $customerId);
        return $medicalCollection->getData();
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getCustomerDataById($id)
    {
        return $this->customerRepository->getById($id);
    }

    public function getEditMedicalHistoryUrl($id)
    {
        return $this->_urlBuilder->getUrl("backend/medical/edit", ['id' => $id]);
    }

    /**
     * @return string|null
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
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
        return __('Medical History');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Medical History');
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
