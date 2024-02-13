<?php

namespace Ssmd\ProductAdditionalContent\Model\ResourceModel\ContentSection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\ProductAdditionalContent\Model\ContentSection', 'Ssmd\ProductAdditionalContent\Model\ResourceModel\ContentSection');
        //$this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>
