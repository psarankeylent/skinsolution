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

namespace ParadoxLabs\Subscriptions\Model\Config\Backend;

/**
 * InstallmentLabel Class
 */
class InstallmentLabel extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);

        $this->resourceConnection = $resourceConnection;
        $this->helper = $helper;
    }

    /**
     * After change Catalog Inventory Backorders value process
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->updateInstallmentLabel();
        }

        return parent::afterSave();
    }

    /**
     * When changing subscription option label, update existing options accordingly.
     *
     * @return void
     */
    protected function updateInstallmentLabel()
    {
        $db = $this->resourceConnection->getConnection();

        /**
         * Update subscription custom options from label X to Y.
         *
         * UPDATE `catalog_product_option_type_title` AS `main_table`
         *  INNER JOIN `paradoxlabs_subscription_product_interval` AS `interval`
         *   ON main_table.option_type_id=interval.value_id
         *  SET `main_table`.`title` = REPLACE(main_table.title, :oldValue, :newvalue)
         *  WHERE (main_table.title LIKE '%:oldValue%')
         *   AND (interval.length IS NOT NULL)
         */

        // Prepare join and update
        $select = $db->select()
            ->joinInner(
                [
                    'interval' => $db->getTableName('paradoxlabs_subscription_product_interval'),
                ],
                'main_table.option_type_id=interval.value_id',
                [
                    'title' => new \Zend_Db_Expr(
                        sprintf(
                            'REPLACE(main_table.title, %s, %s)',
                            $db->quoteInto('?', $this->getOldValue()),
                            $db->quoteInto('?', $this->getValue())
                        )
                    )
                ]
            )
            ->where('main_table.title LIKE ?', '%' . $this->getOldValue() . '%')
            ->where('interval.length IS NOT NULL');

        // Create update from select
        $query = $db->updateFromSelect(
            $select,
            [
                'main_table' => $db->getTableName('catalog_product_option_type_title'),
            ]
        );

        // Execute the update
        $result = $db->query($query);

        /**
         * Log result, if any
         */
        if ($result instanceof \Zend_Db_Statement_Pdo === false || (int)$result->rowCount() === 0) {
            return;
        }

        $this->helper->log(
            'subscriptions',
            __(
                'Updated %1 custom option values from "%2" to "%3".',
                $result->rowCount(),
                $this->getOldValue(),
                $this->getValue()
            )
        );
    }
}
