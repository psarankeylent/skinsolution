<?php
namespace Ssmd\CustomerPhotos\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;


class CustomerPhotos extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\CustomerPhotos\Model\ResourceModel\CustomerPhotos');
    }
}

?>
