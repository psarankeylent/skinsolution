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

use Magento\Framework\Filter\FilterManager;
use Aheadworks\Helpdesk2\Model\DateTime\Formatter;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;

/**
 * Class DataFormatter
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class DataFormatter
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var Formatter
     */
    private $dateTimeFormatter;

    /**
     * @param FilterManager $filterManager
     * @param Formatter $dateTimeFormatter
     */
    public function __construct(
        FilterManager $filterManager,
        Formatter $dateTimeFormatter
    ) {
        $this->filterManager = $filterManager;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Prepare message content
     *
     * @param array $item
     * @return string
     */
    public function prepareMessageContent($item)
    {
        $content = '';
        if (isset($item[GridInterface::LAST_MESSAGE_CONTENT])) {
            $content = $this->filterManager->truncate(
                strip_tags($item[GridInterface::LAST_MESSAGE_CONTENT]),
                ['length' => 500]
            );
        }

        return $content;
    }

    /**
     * Prepare message date
     *
     * @param array $item
     * @return string
     */
    public function prepareMessageDate($item)
    {
        $messageDate = '';
        if (isset($item[GridInterface::LAST_MESSAGE_DATE])
            && $item[GridInterface::LAST_MESSAGE_DATE] !== "0000-00-00 00:00:00"
        ) {
            $messageDate = $this->dateTimeFormatter->formatDate($item[GridInterface::LAST_MESSAGE_DATE]);
        }

        return $messageDate;
    }
}
