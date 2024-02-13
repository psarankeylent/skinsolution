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

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Tags
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class Tags extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[TicketInterface::TAG_NAMES]) && !empty($item[TicketInterface::TAG_NAMES])) {
                    $item['tags'] = $item[TicketInterface::TAG_NAMES];
                    $item[TicketInterface::TAG_NAMES] = array_map(
                        'trim',
                        explode(',', $item[TicketInterface::TAG_NAMES])
                    );
                }
            }
        }
        return $dataSource;
    }
}
