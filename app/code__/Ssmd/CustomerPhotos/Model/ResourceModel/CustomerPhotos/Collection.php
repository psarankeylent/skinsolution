<?php

namespace Ssmd\CustomerPhotos\Model\ResourceModel\CustomerPhotos;

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
        $this->_init('Ssmd\CustomerPhotos\Model\CustomerPhotos',
            'Ssmd\CustomerPhotos\Model\ResourceModel\CustomerPhotos'
        );
    }

}

?>
