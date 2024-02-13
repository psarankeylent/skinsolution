<?php
namespace Renewal\CcReports\Ui\Component\Listing\Columns;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class ActiveSubscriptionCard extends Column
{

    /**
     * @param ContextInterface $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->paymentFactory = $paymentFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $quoteIds = [];
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $paymentId = $item['payment_id'];

                // Getting quote_id from quote_payment table by payment_id
                $quotePayment = $this->paymentFactory->create()->load($paymentId);

                // Getting a list of quote_id from subscriptions table by quote_id and status is active.
                $subscriptionCollection = $this->subscriptionFactory->create()->getCollection();
                $subscriptionCollection->addFieldToFilter('quote_id', $quotePayment['quote_id']);
                $subscriptionCollection->addFieldToFilter('status', 'active');
                //echo "<pre>"; print_r($subscriptionCollection->getData()); exit;

                $card = 'No';
                if(!empty($subscriptionCollection->getData()))
                {
                    $card = 'Yes';
                }
                $item[$fieldName] = $card;
            }

        }
        return $dataSource;
    }
}
