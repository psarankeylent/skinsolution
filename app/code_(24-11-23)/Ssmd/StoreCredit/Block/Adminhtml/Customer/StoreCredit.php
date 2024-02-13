<?php

namespace Ssmd\StoreCredit\Block\Adminhtml\Customer;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


class StoreCredit extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'store_credit.phtml';
    protected $customerFactory;
    protected $formKey;
    protected $collectionFactory;
    protected $adminSession;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Ssmd\CustomerNotes\Model\ResourceModel\CustomerNotes\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Ssmd\StoreCredit\Model\StorecreditFactory $storecreditFactory,
        \Magento\User\Model\UserFactory $userFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        $this->formKey = $formKey;
        $this->collectionFactory = $collectionFactory;
        $this->adminSession = $adminSession;
        $this->storecreditFactory = $storecreditFactory;
        $this->userFactory = $userFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */

    public function getAdminUserNameById($id)
    {
        $user = $this->userFactory->create()->load($id);
        return $user->getUserName();
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

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

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getCustomerName($customerId)
    {
        $customer = $this->customerFactory->create()->load($this->getCustomerId());
        $customerName = $customer->getFirstname().' '.$customer->getLastname();

        return $customerName;
    }

    public function getAdminUserName()
    {
        return $this->adminSession->getUser()->getUsername();
    }
    public function getAdminUserId()
    {
        return $this->adminSession->getUser()->getUserId();
    }

    public function getStoreCreditHistory(){
        $mageCustomerId = $this->getCustomerId();
        $collection = $this->storecreditFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$mageCustomerId)
                        ->setOrder('id','DESC');

        return $collection;
    }


    public function getCurrentStoreCredit(){

        $customerId = $this->getCustomerId();

        $collection = $this->storecreditFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('customer_id',$customerId)
                        ->setOrder('id','DESC');

        if ($collection->count()) {
            return $collection->getFirstItem()->getData()['amount'];
        }
    }
}
