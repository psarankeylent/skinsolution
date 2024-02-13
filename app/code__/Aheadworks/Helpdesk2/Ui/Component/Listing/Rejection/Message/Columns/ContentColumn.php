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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Rejection\Message\Columns;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ContentColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Rejection\Message\Columns
 */
class ContentColumn extends Column
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterManager $filterManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterManager $filterManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->filterManager = $filterManager;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item[RejectedMessageInterface::CONTENT . '_tooltip'] =
                $this->prepareMessageContent($item[RejectedMessageInterface::CONTENT], 500);
            $item[RejectedMessageInterface::CONTENT] =
                $this->prepareMessageContent($item[RejectedMessageInterface::CONTENT], 50);
        }

        return $dataSource;
    }

    /**
     * Prepare message content
     *
     * @param string $content
     * @param int $length
     * @return string
     */
    public function prepareMessageContent($content, $length)
    {
        return $this->filterManager->truncate(
            strip_tags($content),
            ['length' => $length]
        );
    }
}
