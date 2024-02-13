<?php
namespace Ssmd\Impersonation\Model\ResourceModel\Impersonation;

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
        $this->_init('Ssmd\Impersonation\Model\Impersonation',
            'Ssmd\Impersonation\Model\ResourceModel\Impersonation'
        );
    }
}

?>
