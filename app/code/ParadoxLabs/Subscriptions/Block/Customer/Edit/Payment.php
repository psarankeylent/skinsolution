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

namespace ParadoxLabs\Subscriptions\Block\Customer\Edit;

/**
 * Payment Class
 */
class Payment extends \ParadoxLabs\Subscriptions\Block\Customer\View
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $tokenbaseHelper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     */
    protected $paymentService;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $corePaymentHelper;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ParadoxLabs\Subscriptions\Block\Customer\View\Context $viewContext
     * @param \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper
     * @param \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService
     * @param \Magento\Payment\Helper\Data $corePaymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ParadoxLabs\Subscriptions\Block\Customer\View\Context $viewContext,
        \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper,
        \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService,
        \Magento\Payment\Helper\Data $corePaymentHelper,
        array $data
    ) {
        parent::__construct(
            $context,
            $viewContext,
            $data
        );

        $this->tokenbaseHelper = $tokenbaseHelper;
        $this->paymentService = $paymentService;
        $this->corePaymentHelper = $corePaymentHelper;
    }

    /**
     * Get active customer cards.
     *
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface[]
     */
    public function getCustomerCards()
    {
        return $this->paymentService->getActiveCustomerCardsForQuote(
            $this->getSubscription()->getQuote()
        );
    }

    /**
     * Get TokenBase helper class
     *
     * @return \ParadoxLabs\TokenBase\Helper\Data
     */
    public function getTokenbaseHelper()
    {
        return $this->tokenbaseHelper;
    }

    /**
     * Get offline payment method instances
     *
     * @return \Magento\Payment\Model\MethodInterface[]
     */
    public function getOfflinePaymentMethods()
    {
        return $this->paymentService->getOfflineMethods();
    }

    /**
     * Get a payment method's legacy form HTML
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return string
     */
    public function getPaymentForm(\Magento\Payment\Model\MethodInterface $method)
    {
        $method->setInfoInstance(
            $this->getSubscription()->getQuote()->getPayment()
        );

        $block = $this->corePaymentHelper->getMethodFormBlock(
            $method,
            $this->getLayout()
        );

        return $block->toHtml();
    }
}
