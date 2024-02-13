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

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface AutomationSearchResultsInterface
 * @api
 */
interface AutomationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get automation list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface[]
     */
    public function getItems();

    /**
     * Set automation list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
