<?php

namespace Ssmd\CustomTickets\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;

class CustomTickets extends AbstractModel
{

    protected $curl;

   /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\CustomTickets\Model\ResourceModel\CustomTickets');
    }

}

