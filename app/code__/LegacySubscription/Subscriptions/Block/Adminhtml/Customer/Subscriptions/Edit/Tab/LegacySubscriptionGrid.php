<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;


/**
 * Customer Legacy Subscriptions
 */
class LegacySubscriptionGrid extends \Magento\Backend\Block\Widget\Form\Generic
{
   

    protected $status;
    protected $customerSubscriptionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \LegacySubscription\Subscriptions\Model\Config\Source\Status $status,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory,
        array $data = []
    ) {
        $this->status = $status;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        //$model = $this->_coreRegistry->registry('subscriptions_data');

        $model = $this->customerSubscriptionFactory->create()->load($this->getRequest()->getParam('id'));

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Legacy Subscriptions'), 'class' => 'fieldset-wide']);

        
        //if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        //}

        $fieldset->addField(
            'profile_id',
            'text',
            [
                'name' => 'profile_id',
                'label' => __('Profile ID'),
                'title' => __('Profile ID'),
                'required' => false,
                'disabled' => true
            ]
        );
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => false,
                'disabled' => true
            ]
        );
        $fieldset->addField(
            'sku',
            'text',
            [
                'name' => 'sku',
                'label' => __('Sku'),
                'title' => __('Sku'),
                'required' => false,
                'disabled' => true
            ]
        );
        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Subtotal'),
                'title' => __('Subtotal'),
                'required' => false,
                'disabled' => true
            ]
        );

        if($model->getData('status') == 'active'):
            $disabled = false;
        else:
            $disabled = true;
        endif;

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('status'),
                'title' => __('status'),
                'required' => false,
                'disabled' => $disabled,
                'options' => $this->status->getOptions()
            ]
        );
        $fieldset->addField(
            'create_date',
            'text',
            [
                'name' => 'create_date',
                'label' => __('Purchased'),
                'title' => __('Purchased'),
                'required' => false,
                'disabled' => true,
                'renderer' => 'LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer\CreatedDate'
            ]
        );
        $fieldset->addField(
            'customer_id',
            'hidden',
            [
                'name' => 'customer_id',
                'label' => __('Customer ID')
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    
    }
    
}
