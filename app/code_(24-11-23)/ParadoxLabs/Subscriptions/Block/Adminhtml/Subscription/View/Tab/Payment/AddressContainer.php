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

namespace ParadoxLabs\Subscriptions\Block\Adminhtml\Subscription\View\Tab\Payment;

/**
 * AddressContainer Class
 */
class AddressContainer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->layout = $layout;
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getHtml()
    {
        // Add JS component to drive form visibility
        $addressForm = $this->layout->createBlock(
            Address::class,
            'payment_billing_address_form'
        );
        $addressForm->setTemplate('ParadoxLabs_Subscriptions::subscriptions/view/billing-address.phtml');
        return $addressForm->toHtml();
    }
}
