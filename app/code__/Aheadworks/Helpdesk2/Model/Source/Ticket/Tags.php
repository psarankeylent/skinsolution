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

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\CollectionFactory as TagCollectionFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Collection;

/**
 * Class Tags
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class Tags implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $tagCollection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param TagCollectionFactory $tagCollectionFactory
     */
    public function __construct(TagCollectionFactory $tagCollectionFactory)
    {
        $this->tagCollection = $tagCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->tagCollection->toOptionArray();
        }
        return $this->options;
    }
}
