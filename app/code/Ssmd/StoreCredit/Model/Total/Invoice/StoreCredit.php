<?php

namespace Ssmd\StoreCredit\Model\Total\Invoice;

use Ssmd\StoreCredit\Helper\Data as StoreCreditHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magento\Sales\Model\Order\Invoice;

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

	public function collect(Invoice $invoice)
	{
        $order = $invoice->getOrder();
        $quoteId = $order->getQuoteId();

        $baseDiscount = $this->storeCredit->getQuotesStoreCredit($quoteId);

        if ($baseDiscount) {
            $discount = $this->priceCurrency->convert($baseDiscount);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $discount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseDiscount);
            $invoice->setStoreCredit(-$discount);
        }

        return $this;
	}
}
