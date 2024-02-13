<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\ReconciliationInvoiceReport\Model;

use Magento\Framework\Model\AbstractModel;

class ReconciliationInvoiceReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\ReconciliationInvoiceReport\Model\ResourceModel\ReconciliationInvoiceReport::class);
    }

}

