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
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Eav\Api\Data\AttributeInterface;

/**
 * Interface TicketAttributeInterface
 * @api
 */
interface TicketAttributeInterface extends AttributeInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const IS_GLOBAL = 'is_global';
    const IS_VISIBLE_IN_GRID = 'is_visible_in_grid';
    const IS_FILTERABLE_IN_GRID = 'is_filterable_in_grid';

    /**
     * Whether attribute is global
     *
     * @return bool
     */
    public function getIsGlobal();

    /**
     * Set is attribute global
     *
     * @param bool $isGlobal
     * @return $this
     */
    public function setIsGlobal($isGlobal);

    /**
     * Whether it is visible in ticket grid
     *
     * @return bool
     */
    public function getIsVisibleInGrid();

    /**
     * Set is attribute visible in grid
     *
     * @param bool $isVisibleInGrid
     * @return $this
     */
    public function setIsVisibleInGrid($isVisibleInGrid);

    /**
     * Whether it is filterable in ticket grid
     *
     * @return bool|null
     */
    public function getIsFilterableInGrid();

    /**
     * Set is attribute filterable in grid
     *
     * @param bool $isFilterableInGrid
     * @return $this
     */
    public function setIsFilterableInGrid($isFilterableInGrid);
}
