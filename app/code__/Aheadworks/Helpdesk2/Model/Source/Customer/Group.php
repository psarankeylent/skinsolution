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
namespace Aheadworks\Helpdesk2\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class Group
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $customerGroupCollection;

    /**
     * @param Collection $customerGroupCollection
     */
    public function __construct(
        Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
