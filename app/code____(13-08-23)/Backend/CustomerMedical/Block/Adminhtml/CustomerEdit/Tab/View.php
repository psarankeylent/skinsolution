<?php

namespace Backend\CustomerMedical\Block\Adminhtml\CustomerEdit\Tab;

class View extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    protected $scoreCredit;
    protected $_storeManager;
    protected $storecreditFactory;
    protected $storecreditCustomerBalanceFactory;
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
        array $data = []
    ) {
        $this->_coreRegistry        = $registry;
        $this->_storeManager        = $storeManager;
        $this->customerRepository   = $customerRepository;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->medicalFactory       = $medicalFactory;
        parent::__construct($context, $data);
    }


    public function getCustomerMedicalHistory()
    {
        $customerId     = $this->getCustomerId();
        $medicalCollection = $this->medicalFactory->create()->getCollection();
        $medicalCollection->addFieldToFilter('customer_id', $customerId);        
        return $medicalCollection->getData();
    }




    /*
    // Return data array of Customer Medical Histroy
    public function getCustomerMedicalHistory()
    {
        $customerId = $this->getCustomerId();

        try {

            $results=[];
            if(isset($customerId) && $customerId != null){
                $medicalHistoryColl = $this->medicalFactory->create()->getCollection();
                $medicalHistoryColl->addFieldToFilter('customer_id', $customerId);
                $medicalHistoryColl->addFieldToFilter("order_number", array("neq" => "m1-data"));
                $medicalHistoryColl->addFieldToFilter("order_number", array("neq" => null));
                //echo "<pre>"; print_r($medicalHistoryColl->getData()); exit;
                if(!empty($medicalHistoryColl->getData()))
                {
                    $medicalHistoryCo = $this->medicalHistoryFactory->create()->getCollection();
                    $medicalHistoryCo->addFieldToFilter('customer_id', $customerId);
                    $medicalHistoryCo->addFieldToFilter("order_number", array("neq" => "m1-data"));
                    $medicalHistoryCo->addFieldToFilter("order_number", array("neq" => null));
                    $medicalHistoryCo->setOrder('order_number','DESC');
                    $dt = $medicalHistoryCo->getFirstItem();

                    $maxOrderNumber = $dt->getData()['order_number'];
                    if(isset($maxOrderNumber) && $maxOrderNumber != null)          // M2 Data
                    {
                        $medicalHistory = $this->medicalHistoryFactory->create()->getCollection();
                        $medicalHistory->addFieldToFilter('customer_id', $customerId);
                        $medicalHistory->addFieldToFilter('order_number', $maxOrderNumber);
                        $results = $medicalHistory->getData();
                    }
                    else{
                        throw new \Exception("Order Number Issue ");
                    }
                }
                else
                {
                    $medicalHistory = $this->medicalHistoryFactory->create()->getCollection();
                    $medicalHistory->addFieldToFilter('customer_id', $customerId);
                    $medicalHistory->addFieldToFilter('order_number', 'm1-data');     // M1 Data
                    $results = $medicalHistory->getData();
                }

            }
            // echo "<pre>"; print_r($results); exit;
            return $results;
        }
        catch(\Exception $e){
           // $logger->info("Error : ".$e->getMessage());
        }
    }
    */

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
