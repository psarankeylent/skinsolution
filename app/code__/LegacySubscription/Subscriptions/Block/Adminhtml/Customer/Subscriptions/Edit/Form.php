<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Subscriptions\Edit;

/**
 * Form Class
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
       
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                ],
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        parent::_prepareForm();

        return $this;
    }
}
