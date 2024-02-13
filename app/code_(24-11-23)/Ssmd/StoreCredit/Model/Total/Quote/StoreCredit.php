<?php

namespace Ssmd\StoreCredit\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Ssmd\StoreCredit\Helper\Data as StoreCreditHelper;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

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

	public function collect(
		Quote $quote,
		ShippingAssignmentInterface $shippingAssignment,
		Total $total
	)
	{
		parent::collect($quote, $shippingAssignment, $total);

        $baseDiscount = $this->storeCredit->getQuotesStoreCredit($quote->getId());

        if ($baseDiscount) {
            $customerStoreCredits = $this->storeCredit->getCustomerStoreCredits($quote->getCustomerId());

            if ($customerStoreCredits) {
                $baseGrandTotal = (float)$total->getBaseGrandTotal();
                $grandTotal = (float)$total->getGrandTotal();

                $baseDiscount = min($baseGrandTotal, $customerStoreCredits);
                $discount = $this->priceCurrency->convert($baseDiscount);

                $total->addTotalAmount('storecredit', -$discount);
                $total->addBaseTotalAmount('storecredit', -$baseDiscount);

                $total->setBaseGrandTotal($baseGrandTotal - $baseDiscount);
                $total->setGrandTotal($grandTotal - $discount);
                $quote->setAppliedStoreCredit($discount);

                $data = [
                    'customer_id' => $quote->getCustomerId(),
                    'quote_id' => $quote->getId(),
                    'amount' => $discount,
                ];
                $this->storeCredit->applyQuotesStoreCredit($data);
            }
        }

		return $this;
	}

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => 'storecredit',
            'title' => 'Store Credit XYZ',
            'value' => $quote->setAppliedStoreCredit(),
        ];
    }
    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Store Credit ABC');
    }
}
