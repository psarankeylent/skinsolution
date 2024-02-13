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
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = parent::getData();
        if (isset($data['items']) && !empty($data['items'])) {
            $items = $data['items'];
            foreach ($items as $index => &$item) {
                if (isset($items[$index + 1])
                    && $item[GridInterface::CUSTOMER_NAME] == $items[$index + 1][GridInterface::CUSTOMER_NAME]
                ) {
                    $item['css-row-class'] = 'highlight-same-customer-ticket';
                    continue;
                }
                if (isset($items[$index - 1])
                    && $item[GridInterface::CUSTOMER_NAME] == $items[$index - 1][GridInterface::CUSTOMER_NAME]
                ) {
                    $item['css-row-class'] = 'highlight-same-customer-ticket';
                    continue;
                }
                if ($item[GridInterface::LAST_MESSAGE_TYPE] === MessageType::ADMIN) {
                    $item['css-row-class'] = 'highlight-last-reply-admin-ticket';
                    continue;
                }

                $item['css-row-class'] = '';
            }

            $data['items'] = $items;
        }

        return $data;
    }
}
