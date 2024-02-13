<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ProductAdditionalContent\Helper;

/**
 * Html output
 */
class Output extends \Magento\Catalog\Helper\Output
{
    public function productContentHtml($attributeHtml)
    {
        return $this->_getTemplateProcessor()->filter($attributeHtml);
    }
}
