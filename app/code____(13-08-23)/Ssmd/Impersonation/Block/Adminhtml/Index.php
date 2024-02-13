<?php

namespace Ssmd\Impersonation\Block\Adminhtml;

class Index extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {   
        $this->removeButton('add');
    }


    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ssmd\Impersonation\Block\Adminhtml\Impersonation\Grid', 'impersonation.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
