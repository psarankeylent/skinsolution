<?php

namespace Ssmd\ProductTopContent\Model\ResourceModel\ContentSection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\ProductTopContent\Model\ContentSection', 'Ssmd\ProductTopContent\Model\ResourceModel\ContentSection');
    }
}
