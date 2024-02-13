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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class CreationRenderer
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer
 */
class CreationRenderer extends AbstractExtensibleObject implements CreationRendererInterface
{
    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getDepartments()
    {
        return $this->_get(self::DEPARTMENTS);
    }

    /**
     * @inheritdoc
     */
    public function setDepartments($departments)
    {
        return $this->setData(self::DEPARTMENTS, $departments);
    }

    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        return $this->_get(self::REQUEST);
    }

    /**
     * @inheritdoc
     */
    public function setRequest($request)
    {
        return $this->setData(self::REQUEST, $request);
    }
}
