<?php

namespace Ssmd\StoreCredit\Model\Total\Creditmemo;

use Ssmd\StoreCredit\Helper\Data as StoreCreditHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var StoreCreditHelper
     */
    protected  $storeCredit;

    /**
     * @param StoreCreditHelper $storeCredit
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        StoreCreditHelper $storeCredit,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->storeCredit = $storeCredit;
        $this->priceCurrency = $priceCurrency;
    }

    public function collect(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $quoteId = $order->getQuoteId();

        $baseDiscount = $this->storeCredit->getQuotesStoreCredit($quoteId);

        if ($baseDiscount) {
            $discount = $this->priceCurrency->convert($baseDiscount);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $discount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseDiscount);
            $creditmemo->setStoreCredit(-$discount);
        }

        return $this;
    }
}
