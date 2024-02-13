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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class Hydrator
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Hydrator implements HydratorInterface
{
    /**
     * @inheritdoc
     */
    public function extract($entity)
    {
        // todo check object processor can be applied
        return $entity->getData();
    }

    /**
     * @inheritdoc
     */
    public function hydrate($entity, array $data)
    {
        $entity->setData(array_merge($entity->getData(), $data));
        return $entity;
    }
}
