<?php

namespace Ssmd\CustomTickets\Model\ResourceModel\CustomTickets;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\CustomTickets\Model\CustomTickets',
            'Ssmd\CustomTickets\Model\ResourceModel\CustomTickets'
        );
    }

}

?>
