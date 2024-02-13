<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\ImpersonationReport\Model;

use Magento\Framework\Model\AbstractModel;

class Impersonation extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\ImpersonationReport\Model\ResourceModel\Impersonation::class);
    }

}

