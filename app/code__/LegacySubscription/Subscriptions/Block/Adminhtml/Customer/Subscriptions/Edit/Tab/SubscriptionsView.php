<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class SubscriptionsView extends Generic implements TabInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    protected $productFactory;
    protected $collectionFactory;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

       
        $this->customerRepository = $customerRepository;
        $this->collectionFactory = $collectionFactory;
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
    public function getTabLabel()
    {
        return __('Details');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Details');
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {


        /** @var \LegacySubscription\Subscriptions\Model\CustomerSubscription $subscription */
        $subscription = $this->_coreRegistry->registry('current_subscription');

        if(!empty($subscription))
        {
            $subscription = $this->_coreRegistry->registry('current_subscription');
        }
        else
        {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('id', $this->getRequest()->getParam('id'));
            foreach($collection as $item)
            {
                $subscription = $item;
            }
            
        }
        //echo "fsd"; exit;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('subscription_');


        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Subscription Details')]);


        if ($subscription->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
         
        $fieldset->addField(
            'profile_id',
            'text',
            [
                'name'  => 'profile_id',
                'label' => __('Profile ID'),
                'title' => __('Profile ID'),
            ]
        );

        $fieldset->addField(
            'status',
            'text',
            [
                'name'  => 'status',
                'label' => __('Subscription Status'),
                'title' => __('Subscription Status'),
            ]
        );

        $fieldset->addField(
            'amount',
            'text',
            [
                'name'  => 'amount',
                'label' => __('Subtotal'),
                'title' => __('Subtotal'),
            ]
        );
        $fieldset->addField(
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
            ]
        );
        $fieldset->addField(
            'sku',
            'text',
            [
                'name'  => 'sku',
                'label' => __('Sku'),
                'title' => __('Sku'),
            ]
        );
/*
        $fieldset->addField(
            'create_date',
            'date',
            [
                'name'  => 'create_date',
                'label' => __('Create Date'),
                'text'  => $this->_localeDate->formatDateTime(
                    $subscription->getLastRun(),
                    \IntlDateFormatter::MEDIUM
                )
            ]
        ); */

        //$form->setValues($subscription->getData() + $subscription->getAdditionalInformation());
        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_subscription_view_tab_main_prepare_form', ['form' => $form]);

        parent::_prepareForm();

        return $this;
    }
    


}
