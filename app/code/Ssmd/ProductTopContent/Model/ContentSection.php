<?php
namespace Ssmd\ProductTopContent\Model;

class ContentSection extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\ProductTopContent\Model\ResourceModel\ContentSection');
    }
}
