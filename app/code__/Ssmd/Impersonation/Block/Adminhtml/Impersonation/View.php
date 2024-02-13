<?php
namespace Ssmd\Impersonation\Block\Adminhtml\Impersonation;

class View extends \Magento\Backend\Block\Widget\Form\Container
{
    
	/**
	* Core registry
	*
	* @var \Magento\Framework\Registry
	*/
	protected $_coreRegistry = null;
	protected $formKey;
	protected $urlInterface;
	protected $customerFactory;


	/**
	* @param \Magento\Backend\Block\Widget\Context $context
	* @param \Magento\Framework\Registry $registry
	* @param array $data
	*/
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Framework\Registry $registry,
		array $data = []
		) {
		$this->_coreRegistry = $registry;
		$this->formKey = $formKey;
		$this->urlInterface = $urlInterface;
		$this->customerFactory = $customerFactory;
		parent::__construct($context, $data);
	}
	/**
	* Init container
	*
	* @return void
	*/
	protected function _construct() {
		$this->_objectId = 'id';
		$this->_blockGroup = 'Ssmd_Impersonation';
		$this->_controller = 'adminhtml_grid';
		parent::_construct();

	}
	
	public function getFormKey()
	{
	    return $this->formKey->getFormKey();
	}

	public function getPostUrl()
	{
		$id = $this->getRequest()->getParam('id');

	    return $this->urlInterface->getUrl('impersonation/index/save/id/'.$id);
	}

	public function getCustomerData()
	{
		$customer = $this->customerFactory->create()->load($this->getRequest()->getParam('id'));
	    return $customer;
	}
}
