<?php
namespace Ssmd\Impersonation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Impersonation extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('impersonation', 'id');
    }
}

?>
