<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'LegacySubscription_Subscriptions';
        $this->_controller = 'adminhtml';

        parent::_construct();

        // Save customer id in session
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coreSession = $objectManager->get('Magento\Framework\Session\SessionManagerInterface');

        $customerId = $coreSession->getData('mage_customer_id');

        if(isset($customerId) && $customerId != "" )
        {
            $this->addButton(
                'back',
                [
                    'label' => __('Back'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('customer/index/edit/id/'.$customerId) . '\')',
                    'class' => 'back'
                ],
                -1
            );
        }
        else
        {
            $this->addButton(
                'back',
                [
                    'label' => __('Back'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('customer/index/index') . '\')',
                    'class' => 'back'
                ],
                -1
            );
        }

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );


        $this->removeButton('save');
    }



    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        /*if ($this->_coreRegistry->registry('id')->getId()) {
            return __("Edit Legacy '%1'", $this->escapeHtml($this->_coreRegistry->registry('id')->getTitle()));
        } else {
            return __('New Legacy');
        }*/
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('legacy/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }

}
