<?php

namespace CustomReports\BatchListReport\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ExportTransButton extends \Magento\Ui\Component\AbstractComponent
{
    /**
     * Component name
     */
    const NAME = 'exportButton';
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Request\Http $request,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_request = $request;
    }
    /**
     * @return void
     */
    public function prepare()
    {
        $id = $this->_request->getParam('batch_id');
        if (isset($id)) {
            $configData = $this->getData('config');
            if (isset($configData['options'])) {
                $configOptions = [];
                foreach ($configData['options'] as $configOption) {
                    $configOption['url'] = $this->_urlBuilder->getUrl(
                        $configOption['url'],
                        ["batch_id"=>$id]
                    );
                    $configOptions[] = $configOption;
                }
                //echo "<pre>"; print_r($configOptions); exit;
                $configData['options'] = $configOptions;
                $this->setData('config', $configData);
            }
        }
        //parent::prepare();
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
