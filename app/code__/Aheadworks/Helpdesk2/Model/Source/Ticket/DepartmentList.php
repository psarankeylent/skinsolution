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
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection as Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\CollectionFactory as CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DepartmentList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class DepartmentList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFilter(DepartmentInterface::IS_ACTIVE, 1);
            $collection->setOrder(DepartmentInterface::SORT_ORDER, Collection::SORT_ORDER_ASC);

            $this->options = $collection->toOptionArray();
        }

        return $this->options;
    }

    /**
     * Get option by option id
     *
     * @param int $optionId
     * @return array|null
     */
    public function getOptionById($optionId)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $optionId) {
                return $option;
            }
        }

        return null;
    }
}
