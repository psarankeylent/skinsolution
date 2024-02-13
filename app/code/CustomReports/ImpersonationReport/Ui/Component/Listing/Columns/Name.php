<?php
namespace CustomReports\ImpersonationReport\Ui\Component\Listing\Columns;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Name extends Column
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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
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

                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                //$item['firstname'] = "";

                if(isset($item['customer_email']) && $item['customer_email']!= "")
                {
                    $customer_email = $item['customer_email'];
                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->loadByEmail($customer_email);
                    $item['firstname'] = $customer->getFirstname().' '.$customer->getLastname();

                }
                $newAr[] = $item;
            }
            /// echo "<pre>"; print_r($newAr); exit;

        }

        return $dataSource;
    }
}
