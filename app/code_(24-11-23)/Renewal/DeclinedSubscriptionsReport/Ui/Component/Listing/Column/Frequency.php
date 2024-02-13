<?php

namespace Renewal\DeclinedSubscriptionsReport\Ui\Component\Listing\Column;

class Frequency extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components = []
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])){
            $fieldName = $this->getData('name');
            foreach($dataSource['data']['items'] as &$item){
                $item[$fieldName] = $item['frequency_count'] . ' ' . $item['frequency_unit'];
            }
        }

        return $dataSource;
    }
}
