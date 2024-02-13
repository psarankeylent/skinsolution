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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\QuickResponse as QuickResponseModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = QuickResponseInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(QuickResponseModel::class, QuickResponseResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    protected function getStorefrontLabelEntityType()
    {
        return QuickResponseInterface::STOREFRONT_LABEL_ENTITY_TYPE;
    }
}
