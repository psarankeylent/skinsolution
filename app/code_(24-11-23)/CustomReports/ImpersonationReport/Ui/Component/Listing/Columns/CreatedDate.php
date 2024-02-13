<?php
namespace CustomReports\ImpersonationReport\Ui\Component\Listing\Columns;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class CreatedDate extends Column
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $utcConverter = $objectManager->get(\Magento\Framework\Stdlib\DateTime\Timezone\LocalizedDateToUtcConverterInterface::class);
        $timezone = $objectManager->get(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
        $datetime = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                   $dateTime = $item['created_date'];
                   

                    // you can also get format wise date and time
                  //  $dateTime = $timezone->date(new \DateTime($item['created_date']))->format('Y-m-d H:i:s');
                        
                   //$newDate = date('Y-m-d H:i:s', strtotime($created_date. '-1 days')); // (UTC TIme)more 5hours & 30minutes hours;
                   
                   //$item['created_date'] = $utcConverter->convertLocalizedDateToUtc($created_date);
                    //$newAr[] = $datetime->gmtDate('Y-m-d H:i:s',$item['created_date']);
                   

                   //\Zend_Debug::dump($date->format('Y-m-d H:i:s'));
                  // $newAr[] = $date->format('Y-m-d h:i:s A');

                  // $newAr[] = $item['created_date'];

                  /* $newDate = $timezone->date(
                        new \DateTime(
                            $item['created_date'],
                            new \DateTimeZone('GMT')
                        )
                    );
                   echo $item['id'];
                   echo "<br>";
                   echo $item['created_date'];
                   echo "<br>";
                   $newDate->format('Y-m-d H:i:s');*/

                   $item[$fieldName] = $dateTime;
            }

            //echo "<pre>"; print_r($newAr); exit;

        }

        return $dataSource;
    }
}
