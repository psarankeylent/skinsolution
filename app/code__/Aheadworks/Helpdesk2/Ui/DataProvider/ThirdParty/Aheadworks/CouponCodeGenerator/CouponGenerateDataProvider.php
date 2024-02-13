<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Ui\DataProvider\ThirdParty\Aheadworks\CouponCodeGenerator;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class CouponGenerateDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\ThirdParty\Aheadworks\CouponCodeGenerator
 */
class CouponGenerateDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var ProcessorInterface
     */
    private $formDataProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RequestInterface $request
     * @param ProcessorInterface $formDataProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        ProcessorInterface $formDataProcessor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->formDataProcessor = $formDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $preparedData = [];

        $ticketId = $this->request->getParam($this->getRequestFieldName());
        if ($ticketId) {
            $preparedData[$ticketId] = $this->formDataProcessor->prepareEntityData(['ticket_id' => $ticketId]);
        }

        return $preparedData;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->formDataProcessor->prepareMetaData($meta);

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }
}
