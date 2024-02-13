<?php

namespace CustomReports\BatchListReport\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Stats extends Column
{
    /** Url path */
    const ROW_VIEW_URL = 'reports/batchstatsreport/index/';
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_viewUrl;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $viewUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $viewUrl = self::ROW_VIEW_URL
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_viewUrl = $viewUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['batch_id'])) {
                    $item[$name]['view'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_viewUrl,
                            ['batch_id' => $item['batch_id']]
                        ),
                        'label' => __('View'),
                        'target' => '_blank'
                    ];
                }
            }
        }

        return $dataSource;
    }
}
