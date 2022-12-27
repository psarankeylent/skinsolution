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

namespace ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription;

/**
 * Subscription collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'paradoxlabs_subscription_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'subscription_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            \ParadoxLabs\Subscriptions\Model\Subscription::class,
            \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription::class
        );
    }

    /**
     * Join quote currency code.
     *
     * @return $this
     */
    public function joinQuoteCurrency()
    {
        $this->join(
            [
                'quote' => $this->getTable('quote'),
            ],
            'quote.entity_id=main_table.quote_id',
            [
                'quote_currency_code',
            ]
        );

        return $this;
    }

    /**
     * Join tokenbase card.
     *
     * @return $this
     */
    public function joinPaymentCard()
    {
        $this->join(
            [
                'quote_payment' => $this->getTable('quote_payment'),
            ],
            'quote_payment.quote_id=main_table.quote_id',
            [
                'tokenbase_id',
            ]
        );

        return $this;
    }

    /**
     * Prevent toolbar sort errors.
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = 'asc')
    {
        /**
         * This is a stub method to prevent errors from bad extensions that try to sort toolbars without checking what
         * those toolbar collections actually are. This call will never be accurate and we can't verify $field exists,
         * so we'll just ignore it and prevent the call from causing a PHP error.
         */

        return $this;
    }
}
