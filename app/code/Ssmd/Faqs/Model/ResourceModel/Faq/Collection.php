<?php

namespace Ssmd\Faqs\Model\ResourceModel\Faq;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\Faqs\Model\Faq', 'Ssmd\Faqs\Model\ResourceModel\Faq');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>