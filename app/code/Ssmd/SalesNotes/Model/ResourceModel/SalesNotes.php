<?php

namespace Ssmd\SalesNotes\Model\ResourceModel;

class SalesNotes extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('sales_notes', 'id');
    }
}
