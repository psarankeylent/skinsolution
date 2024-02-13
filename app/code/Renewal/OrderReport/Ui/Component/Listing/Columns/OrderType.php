<?php
namespace Renewal\OrderReport\Ui\Component\Listing\Columns;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class OrderType extends Column
{

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
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
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
        $objectManager          = \Magento\Framework\App\ObjectManager::getInstance();
        $subscription           = $objectManager->create("ParadoxLabs\Subscriptions\Model\Log");

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $orderId = $item['entity_id'];

                $subscriptionCollection = $subscription->getCollection();
                $subscriptionCollection->addFieldToFilter("order_id", $orderId);

                if(!empty($subscriptionCollection->getData())){
                    $type = 'Subscription';
                }else{
                    $type = 'Onetime';
                }

                $item[$fieldName] = $type;
            }
        }

        return $dataSource;
    }
}
