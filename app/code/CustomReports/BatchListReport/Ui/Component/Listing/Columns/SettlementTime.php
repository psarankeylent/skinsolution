<?php
namespace CustomReports\BatchListReport\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class SettlementTime extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->medicalFactory = $medicalFactory;
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
        $timezoneInterface = $objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        if (isset($dataSource['data']['items'])) {

            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {

                $utcDate = $item['settlement_time_local'];
                $convertedDateTime = str_replace("-","/",$utcDate);

                $settlementLocalTime = $timezoneInterface->date(new \DateTime($convertedDateTime))->format('m-d-Y H:i A');
                $item[$fieldName] = $settlementLocalTime;
            }


        }

        return $dataSource;
    }
}
