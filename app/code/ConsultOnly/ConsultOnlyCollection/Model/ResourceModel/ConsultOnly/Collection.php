<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    //protected $_idFieldName = 'consultonly_id';
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnly::class,
            \ConsultOnly\ConsultOnlyCollection\Model\ResourceModel\ConsultOnly::class
        );
    }
}

