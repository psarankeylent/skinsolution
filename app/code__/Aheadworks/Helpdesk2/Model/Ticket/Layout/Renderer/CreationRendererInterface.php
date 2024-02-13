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

use Aheadworks\Helpdesk2\Model\Ticket\Layout\RendererInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Interface CreationRendererInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer
 */
interface CreationRendererInterface extends RendererInterface
{
    const DEPARTMENTS = 'departments';
    const REQUEST = 'request';

    /**
     * Get departments
     *
     * @return DepartmentInterface[]
     */
    public function getDepartments();

    /**
     * Set departments
     *
     * @param DepartmentInterface[] $departments
     * @return $this
     */
    public function setDepartments($departments);

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Set request
     *
     * @param $request
     * @return $this
     */
    public function setRequest($request);
}
