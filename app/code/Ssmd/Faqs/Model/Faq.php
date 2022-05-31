<?php
namespace Ssmd\Faqs\Model;

class Faq extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\Faqs\Model\ResourceModel\Faq');
    }
}
?>