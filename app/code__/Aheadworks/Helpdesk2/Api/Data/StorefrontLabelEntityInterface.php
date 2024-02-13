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

/**
 * Interface StorefrontLabelEntityInterface
 * @api
 */
interface StorefrontLabelEntityInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const STOREFRONT_LABELS = 'storefront_labels';
    const CURRENT_STOREFRONT_LABEL = 'current_storefront_label';
    /**#@-*/

    /**
     * Get array of labels on storefront per store view
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface[]
     */
    public function getStorefrontLabels();

    /**
     * Set array of labels on storefront per store view
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface[] $labelArray
     * @return $this
     */
    public function setStorefrontLabels($labelArray);

    /**
     * Get labels on storefront for current store view
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface
     */
    public function getCurrentStorefrontLabel();

    /**
     * Set labels on storefront for current store view
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface $label
     * @return $this
     */
    public function setCurrentStorefrontLabel($label);
}
