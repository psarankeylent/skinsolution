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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Magento\Framework\DataObject;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email\CollectionFactory;

/**
 * Class Loader
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class Loader
{
    /**
     * @var CollectionFactory
     */
    private $mailCollectionFactory;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param CollectionFactory $mailCollectionFactory
     * @param int $limit
     */
    public function __construct(
        CollectionFactory $mailCollectionFactory,
        $limit = 50
    ) {
        $this->limit = $limit;
        $this->mailCollectionFactory = $mailCollectionFactory;
    }

    /**
     * Load unprocessed emails
     *
     * @return DataObject[]|EmailInterface[]
     */
    public function loadUnprocessedEmails()
    {
        /** @var Collection $mailCollection*/
        $mailCollection = $this->mailCollectionFactory->create();

        $mailCollection
            ->addUnprocessedFilter()
            ->setPageSize($this->limit);

        return $mailCollection->getItems();
    }

    /**
     * Load email by id
     *
     * @return EmailInterface|DataObject
     */
    public function loadById($id)
    {
        /** @var Collection $mailCollection*/
        $mailCollection = $this->mailCollectionFactory->create();

        $mailCollection
            ->addFieldToFilter(EmailInterface::ID, $id);

        return $mailCollection->getFirstItem();
    }
}
