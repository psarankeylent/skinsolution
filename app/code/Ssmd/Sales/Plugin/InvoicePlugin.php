<?php

namespace Ssmd\Sales\Plugin;

use Magento\Sales\Model\Order\Invoice;

class InvoicePlugin
{
    public function beforeRegister(Invoice $invoice)
    {
        $orderIncrementId = $invoice->getOrder()->getIncrementId();
        if (substr($orderIncrementId, 0, 2) === "91") {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invoicing and payment capturing for this order $orderIncrementId is taken care in M1."));
        }
    }
}

