<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Plugin\Quote\Model\Quote\Item\Updater;

/**
 * Plugin Class
 */
class Plugin
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * Plugin constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager
    ) {
        $this->itemManager = $itemManager;
    }

    /**
     * When adding/updating items on the admin order page, update subscription price to match the selection.
     * Otherwise, the custom price option never updates.
     *
     * @param \Magento\Quote\Model\Quote\Item\Updater $subject
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $info
     * @return array|void
     */
    public function beforeUpdate(
        \Magento\Quote\Model\Quote\Item\Updater $subject,
        \Magento\Quote\Model\Quote\Item $item,
        array $info
    ) {
        if (!$item->isObjectNew()) {
            return;
        }

        $isSubscription = $this->itemManager->isSubscription($item);

        if ($isSubscription === true && $item->getOrigData('original_custom_price') === null) {
            // If we've selected a new subscription option, set its price.
            $info['custom_price'] = $item->getOriginalCustomPrice();
        } elseif ($isSubscription === false && $item->getProduct()->getData('subscription_active')) {
            // If we've selected a new non-subscription option, unset any custom price.
            unset($info['custom_price']);
        }

        return [
            $item,
            $info
        ];
    }
}
