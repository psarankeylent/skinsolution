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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Model\Entity\ScopeInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\ResourceModel\AttributePersistor as EavAttributePersistor;
use Aheadworks\Helpdesk2\Model\Ticket\Attribute as TicketEavAttribute;

/**
 * Class AttributePersistor
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class AttributePersistor extends EavAttributePersistor
{
    /**
     * @inheritdoc
     */
    protected function getScopeValue(ScopeInterface $scope, AbstractAttribute $attribute, $useDefault = false)
    {
        if ($attribute instanceof TicketEavAttribute) {
            $useDefault = $useDefault || $attribute->isScopeGlobal();
        }

        return parent::getScopeValue($scope, $attribute, $useDefault);
    }
}
