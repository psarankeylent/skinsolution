<?php

namespace Ssmd\Customer\Block;

use Magento\Framework\View\Element\Template;

class Email extends Template
{
    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getFrontEndUrl()
    {
        return 'https://qafe.skinsolutions.md/';
    }
}
