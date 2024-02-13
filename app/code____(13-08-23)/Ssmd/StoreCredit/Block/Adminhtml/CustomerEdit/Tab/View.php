<?php

namespace Ssmd\StoreCredit\Block\Adminhtml\CustomerEdit\Tab;

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
    protected $_template = 'tab/customer_view.phtml';

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ssmd\StoreCredit\Model\StorecreditFactory $storecreditFactory,
        \Ssmd\StoreCredit\Model\StorecreditCustomerBalanceFactory $storecreditCustomerBalanceFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->storecreditFactory = $storecreditFactory;
        $this->storecreditCustomerBalanceFactory = $storecreditCustomerBalanceFactory;

        parent::__construct($context, $data);
    }

    public function getStoreCreditByCustomerId($customerId){

        $collection = $this->storecreditFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customerId)
                        ->setOrder('id','DESC');

        if($collection->count()) {
            return $collection->getFirstItem()->getData()['credits'];
        }else{
            // Need to check with storecredit_customer_balance table if customer has store credits in Columns AMOUNT
            $balanceCollection = $this->storecreditCustomerBalanceFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('customer_id',$customerId)
                            ->setOrder('balance_id','DESC');

            if($balanceCollection->count()) {
                return $balanceCollection->getFirstItem()->getData()['amount'];
            }else{
                return 0;   
            }
        }

    }

    public function getStoreCreditHistoryByCustomerId($customerId){

        $collection = $this->storecreditFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customerId)
                        ->setOrder('id','DESC');

        if($collection->count()) {
            return $collection->getData();
            //print_r($collection->getFirstItem()->getData());
        }else{
            return false;
        }
    }

    public function getCustomerDataById($id)
    { 
    return $this->customerRepository->getById($id);
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
     * @return string|null
     */

    /*
    public function getStoreCreditAmount($custId)
    {
        $creditData = $this->scoreCredit->create()->getCollection()
                            ->addFieldToSelect("*")
                            ->addFieldToFilter('customer_id', ['eq' => $custId])
                            ->load()->getData();
        return $creditData;                                
    }
    */

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Store Credit');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Store Credit');
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
