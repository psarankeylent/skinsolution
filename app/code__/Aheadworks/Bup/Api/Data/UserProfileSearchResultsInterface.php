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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface UserProfileSearchResultsInterface
 * @api
 */
interface UserProfileSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get user profile list
     *
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface[]
     */
    public function getItems();

    /**
     * Set user profile list
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
