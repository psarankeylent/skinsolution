<?php

namespace Backend\CustomerPhotos\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image';
    const ALT_FIELD = 'name';
    protected $storeManager;
    protected $photoHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        \Ssmd\CustomerPhotos\Helper\Data $photoHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->photoHelper = $photoHelper;
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
            $imageBasePath = $this->photoHelper->getRootPath();

            foreach ($dataSource['data']['items'] as & $item) {
                $path = $imageBasePath.'/'.$item['path'];

                if ($item['path']) {
                    $item[$fieldName . '_src'] = $path;
                   // $item[$fieldName . '_alt'] = $item['title'];
                    $item[$fieldName . '_orig_src'] = $path;
                }else{
                    // please place your placeholder image at pub/media/backend/orderphotos/placeholder/placeholder.jpg
                   // $item[$fieldName . '_src'] = $path.'backend/photos/placeholder/placeholder.jpg';
                    //$item[$fieldName . '_alt'] = 'Place Holder';
                    //$item[$fieldName . '_orig_src'] = $path.'backend/photos/placeholder/placeholder.jpg';
                }
            }
        }

        return $dataSource;
    }
}
