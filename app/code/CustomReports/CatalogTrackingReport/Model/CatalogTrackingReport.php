<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\CatalogTrackingReport\Model;

use Magento\Framework\Model\AbstractModel;

class CatalogTrackingReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\CatalogTrackingReport\Model\ResourceModel\CatalogTrackingReport::class);
    }

}

