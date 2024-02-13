<?php

namespace Backend\Medical\Block\Adminhtml\Medical;

use Magento\Backend\Block\Template;

class View extends Template
{
    public $_template = 'Backend_Medical::tab/viewMedicalHistory.phtml';

    protected $customerFactory;
    protected $formKey;
    protected $medicalFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Backend\Medical\Model\MedicalFactory $medicalFactory
    ){
        $this->customerFactory = $customerFactory;
        $this->formKey = $formKey;
        $this->medicalFactory = $medicalFactory;
        parent::__construct($context);

    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    public function getMedicalHistory($id)
    {
        $data = array();
        
        $model = $this->medicalFactory->create()->load($id);
        $data = $model->getData();

        return $data;
    }
}
