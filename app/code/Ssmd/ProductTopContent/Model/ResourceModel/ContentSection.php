<?php
namespace Ssmd\ProductTopContent\Model\ResourceModel;

class ContentSection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('product_content_sections', 'id');
    }
}
