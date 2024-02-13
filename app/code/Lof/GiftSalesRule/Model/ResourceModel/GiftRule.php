<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;

/**
 * GiftRule resource model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class GiftRule extends AbstractDb
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var MetadataPool */
    protected $metadataPool;

    /**
     * GiftRule constructor.
     *
     * @param Context       $context        Context
     * @param EntityManager $entityManager  Entity manage
     * @param MetadataPool  $metadataPool   Metadata poll
     * @param null          $connectionName Connection name
     */
    public function __construct(
        Context       $context,
        EntityManager $entityManager,
        MetadataPool  $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        $connectionName = $this->metadataPool->getMetadata(GiftRuleInterface::class)->getEntityConnectionName();

        return $this->_resources->getConnectionByName($connectionName);
    }

    /**
    * {@inheritdoc}
    */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $objectId = $this->getObjectId($value, $field);

        if ($objectId) {
            $object->beforeLoad($value, $field);
            $this->entityManager->load($object, $objectId);
            $object->afterLoad();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            GiftRuleInterface::TABLE_NAME,
            GiftRuleInterface::RULE_ID
        );
    }

    /**
     * Get the id of an object
     *
     * @param mixed       $value Value
     * @param string|null $field Field
     *
     * @return bool|int|string
     */
    protected function getObjectId($value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(GiftRuleInterface::class);
        if ($field === null) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;

        if ($field != $entityMetadata->getIdentifierField()) {
            $field = $this->getConnection()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
            $select = $this->getConnection()->select()->from($this->getMainTable())->where($field . '=?', $value);

            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }

        return $entityId;
    }

    public function updateQuoteItemOption($quoteItemId, $product =null){
        $return = false;
        if($product && is_array($product) && isset($product['gift_rule']) && $product['gift_rule']){
            $connection = $this->getConnection();
            $gift_rule_id = (int)$product['gift_rule'];
            $tableName = $connection->getTableName("quote_item_option");

            $whereOption1 = "item_id=".(int)$quoteItemId." AND code ='option_gift_rule'";
            $whereOption2 = "item_id=".(int)$quoteItemId." AND code ='option_ids'";
            //$whereOption3 = "item_id=".(int)$quoteItemId." AND code ='info_buyRequest'";

            $select1 =  $connection->select()->from($tableName, 'option_id')->where($whereOption1);
            $select2 =  $connection->select()->from($tableName, 'option_id')->where($whereOption2);
            //$select3 =  $connection->select()->from($tableName, ['option_id', 'value'])->where($whereOption3);

            $option_id1 = (int)$connection->fetchOne($select1);
            $option_id2 = (int)$connection->fetchOne($select2);
            //$option3 = $connection->fetchOne($select3);
            $tableData = [];
            if(!$option_id1){
                $tableData[] = [
                    "item_id" => $quoteItemId,
                    "code" => "option_gift_rule",
                    "value" => $gift_rule_id
                ];
            }
            if(!$option_id2){
                $tableData[] = [
                    "item_id" => $quoteItemId,
                    "code" => "option_ids",
                    "value" => "gift_rule"
                ];
            }
            if($tableData){
                $connection->insertMultiple($tableName, $tableData);
            }
            
            $return = true;
        }
        return $return;
    }

    public function updateQuoteItemPrice($quoteItemId){
        $tableName = $this->getConnection()->getTableName("quote_item");
        $data = [
            "price" => 0.0000,
            "base_price" => 0.0000,
            "custom_price" => 0.0000,
            "tax_amount" => 0.0000,
            "base_tax_amount" => 0.0000,
            "row_total" => 0.0000,
            "base_row_total" => 0.0000,
            "price_incl_tax" => 0.0000,
            "base_price_incl_tax" => 0.0000,
            "row_total_incl_tax" => 0.0000,
            "base_row_total_incl_tax" => 0.0000,
            "original_custom_price" => 0.0000,
            "base_discount_amount" => 0.0000,
            "discount_amount" => 0.0000
        ];
        $where = ['item_id = ?' => (int)$quoteItemId];
        $updatedRows = $this->getConnection()->update($tableName, $data, $where);
        
        return $updatedRows;
    }
}
