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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag;

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Tag;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as ResourceTag;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Tag::class, ResourceTag::class);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray(TagInterface::ID, TagInterface::NAME);
    }
}
