<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Block\Customer;

use Magento\Framework\View\Element\Template;

/**
 * Edit Class
 */
class Edit extends View
{
    /**
     * Get subscription save URL.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl('*/*/editPost', ['id' => $this->getSubscription()->getId()]);
    }

    /**
     * Get subscription view URL.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->_urlBuilder->getUrl('*/*/view', ['id' => $this->getSubscription()->getId()]);
    }
}
