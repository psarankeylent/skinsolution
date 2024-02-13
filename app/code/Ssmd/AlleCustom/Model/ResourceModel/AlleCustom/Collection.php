<?php
namespace Ssmd\AlleCustom\Model\ResourceModel\AlleCustom;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\AlleCustom\Model\AlleCustom',
            'Ssmd\AlleCustom\Model\ResourceModel\AlleCustom'
        );
    }
}

?>
