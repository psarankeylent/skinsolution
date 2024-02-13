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
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Thread;

use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface as ThreadDataProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread\ProviderInterface as ThreadDataProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class AllListing
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Message
 */
class AllProvider extends AbstractDataProvider
{
    /**
     * @var ThreadDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var ThreadDataProcessorInterface
     */
    private $dataProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ThreadDataProviderInterface $dataProvider
     * @param ThreadDataProcessorInterface $dataProcessor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ThreadDataProviderInterface $dataProvider,
        ThreadDataProcessorInterface $dataProcessor,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->dataProvider = $dataProvider;
        $this->dataProcessor = $dataProcessor;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $ticketId = $this->request->getParam($this->getRequestFieldName());
        return $this->dataProcessor->prepareEntityData(
            $this->dataProvider->getData($ticketId)
        );
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->dataProcessor->prepareMetaData($meta);

        return $meta;
    }
}
