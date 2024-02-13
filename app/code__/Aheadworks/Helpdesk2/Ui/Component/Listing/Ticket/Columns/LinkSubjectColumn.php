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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;

/**
 * Class LinkSubjectColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class LinkSubjectColumn extends Column
{
    /**
     * @var DataFormatter
     */
    private $dataFormatter;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param DataFormatter $dataFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        DataFormatter $dataFormatter,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->dataFormatter = $dataFormatter;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
        $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
        $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
        $cancelingLinkValues = $this->getData('config/cancelingLinkValues') ?? [];
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName . '_label'] = $item[$fieldName];
            if (!in_array($item[$entityFieldName], $cancelingLinkValues, true)) {
                $item[$fieldName . '_url'] = $this->context->getUrl(
                    $viewUrlPath,
                    [$urlEntityParamName => $item[$entityFieldName]]
                );
            }
            $item[GridInterface::LAST_MESSAGE_CONTENT] = $this->dataFormatter->prepareMessageContent($item);
            $item[GridInterface::LAST_MESSAGE_DATE . '_formatted'] = $this->dataFormatter->prepareMessageDate($item);
        }

        return $dataSource;
    }
}
