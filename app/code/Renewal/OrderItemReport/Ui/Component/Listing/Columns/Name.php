<?php
namespace Renewal\OrderItemReport\Ui\Component\Listing\Columns;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Name extends Column
{
    protected $orderRepository;

    /**
     * @param ContextInterface $context
     * @param CustomerFactory $customerFactory
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->orderRepository = $orderRepository;
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
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {

                $order = $this->orderRepository->get($item['order_id']);                

                $firstName = $order->getData('customer_firstname');
                $lastName  = $order->getData('customer_lastname');

                $item[$fieldName] = $firstName.' '.$lastName;
            }
        }

        return $dataSource;
    }
}
