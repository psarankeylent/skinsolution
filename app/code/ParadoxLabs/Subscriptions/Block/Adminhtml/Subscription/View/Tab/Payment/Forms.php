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
 * Forms Class
 */
class Forms extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $corePaymentHelper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     */
    protected $paymentService;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Payment\Helper\Data $corePaymentHelper
     * @param \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Payment\Helper\Data $corePaymentHelper,
        \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService,
        \Magento\Framework\View\LayoutInterface $layout,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->corePaymentHelper = $corePaymentHelper;
        $this->paymentService = $paymentService;
        $this->layout = $layout;
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getHtml()
    {
        $quote = $this->getData('quote');
        if ($quote instanceof \Magento\Quote\Model\Quote === false) {
            return '';
        }

        $html = '';
        foreach ($this->paymentService->getOfflineMethods() as $method) {
            if (empty($method->getFormBlockType())) {
                continue;
            }

            $html .= $this->getPaymentForm($method, $quote);
        }

        // Clean up styling -- the classes used for the admin order form don't work cleanly in this context
        $html = str_replace(
            [
                'admin__field-label',
                'admin__field-control',
                'admin__fieldset',
                'admin__field',
                '_required',
            ],
            [
                'admin__field-label label',
                'admin__field-control control',
                'admin__fieldset fieldset',
                'admin__field field',
                '_required required',
            ],
            $html
        );

        // Add JS component to drive form visibility
        $jsComponent = $this->layout->createBlock(
            \Magento\Framework\View\Element\Template::class,
            'payment_form_js'
        );
        $jsComponent->setTemplate('ParadoxLabs_Subscriptions::subscriptions/view/billing-js.phtml');
        $jsComponent->setData('offline_methods', array_keys($this->paymentService->getOfflineMethods()));
        $html .= $jsComponent->toHtml();

        return $html;
    }

    /**
     * Get a payment method's legacy form HTML
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    public function getPaymentForm(\Magento\Payment\Model\MethodInterface $method, \Magento\Quote\Model\Quote $quote)
    {
        $method->setInfoInstance(
            $quote->getPayment()
        );

        $block = $this->corePaymentHelper->getMethodFormBlock(
            $method,
            $this->layout
        );

        return $block->toHtml();
    }
}
