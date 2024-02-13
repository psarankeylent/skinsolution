<?php
namespace Ssmd\CustomerPhotos\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerPhotos extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_photos', 'photo_id');
    }
}

?>
