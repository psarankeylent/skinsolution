<?php
namespace Ssmd\AlleCustom\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AlleCustom extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('alle_member_customers', 'id');
    }
}

?>
