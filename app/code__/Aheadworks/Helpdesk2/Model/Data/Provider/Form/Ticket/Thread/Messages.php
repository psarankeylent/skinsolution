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
namespace Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread;

use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;

/**
 * Class Messages
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket
 */
class Messages implements ProviderInterface
{
    /**
     * @var MessageCollection
     */
    protected $collection;

    /**
     * @param MessageCollectionFactory $collectionFactory
     */
    public function __construct(MessageCollectionFactory $collectionFactory)
    {
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @param int $ticketId
     * @return array
     */
    public function getData($ticketId)
    {
        $this->collection
            ->addTicketFilter($ticketId)
            ->sortByCreatedAt(SortOrder::SORT_DESC);

        return $this->collection->toArray();
    }
}
