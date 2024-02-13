<?php
namespace Renewal\CcReports\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Telephone extends Column
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
        $telephone = "";
        if (isset($dataSource['data']['items'])) {

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {                
                $customerId = $item['customer_id'];

                $collection = $this->medicalFactory->create()->getCollection();
                $collection->addFieldToFilter('customer_id', $customerId);
                $collection->addFieldToFilter('question_id', 6);    // Phone Number
                $items = $collection->getFirstItem();
                //$collection->setOrder('entity_id', 'DESC');
                //$collection->getSelect()->limit(1);

                //echo "<pre>"; print_r($items->getData()); exit;
                
                if(!empty($items->getData()))
                { 
                    $item[$fieldName] = $items['response'];
                } 
                else{
                    $item[$fieldName] = null;
                }         
            }
                
        }

        return $dataSource;
    }
}
