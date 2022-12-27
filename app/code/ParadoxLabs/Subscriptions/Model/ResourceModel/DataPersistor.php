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

namespace ParadoxLabs\Subscriptions\Model\ResourceModel;

/**
 * Class DataPersistor
 */
class DataPersistor
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * DataPersistor constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        return $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    }

    /**
     * Insert a set of data into a given DB table by straight query.
     *
     * @param string $table Table name to insert into.
     * @param array $columns Column names to insert. Must match $inserts data in num and order.
     * @param array $inserts Values to insert. Flat array expected, but two (arrays of sets) also ok.
     * @param array $updateCols Columns to update on duplicate key.
     * @param int $batchSize Number of values to insert per query. May need to tune to avoid query length errors.
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertData(
        string $table,
        array $columns,
        array $inserts,
        array $updateCols = [],
        int $batchSize = 2500
    ): int {
        $insertSql = $this->makeInsertSql($table);
        $insertSql .= $this->makeDuplicateSql($updateCols, false);

        return $this->processInsert($insertSql, $columns, $inserts, $batchSize);
    }

    /**
     * Insert a set of data into a given DB table by straight query. On duplicate, add to existing values.
     *
     * @param string $table Table name to insert into.
     * @param array $columns Column names to insert. Must match $inserts data in num and order.
     * @param array $inserts Values to insert. Flat array expected, but two (arrays of sets) also ok.
     * @param array $updateCols Columns to update on duplicate key.
     * @param int $batchSize Number of values to insert per query. May need to tune to avoid query length errors.
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    public function insertIncrementData(
        string $table,
        array $columns,
        array $inserts,
        array $updateCols = [],
        int $batchSize = 2500
    ): int {
        $insertSql = $this->makeInsertSql($table);
        $insertSql .= $this->makeDuplicateSql($updateCols, true);

        return $this->processInsert($insertSql, $columns, $inserts, $batchSize);
    }

    /**
     * Process inserts in batches with the given SQL and columns.
     *
     * @param string $insertSql
     * @param array $columns
     * @param array $inserts
     * @param int $batchSize
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    protected function processInsert(
        string $insertSql,
        array $columns,
        array $inserts,
        int $batchSize
    ): int {
        if (is_array(current($inserts))) {
            $inserts = $this->flattenArray($inserts);
        }

        if (empty($inserts)) {
            return 0;
        }

        // Create bind patterns for the inserts
        $binds = $this->makeBindPatterns(count($columns), count($inserts));

        // Split into chunks to limit query size
        $bindChunks   = array_chunk($binds, $batchSize);
        $insertChunks = array_chunk($inserts, $batchSize * count($columns));

        // Wrap up and insert the data
        return $this->processInsertChunks(
            $insertSql,
            $bindChunks,
            $columns,
            $insertChunks
        );
    }

    /**
     * Execute the given prepared SQL batches.
     *
     * @param string $insertSql
     * @param array $bindChunks
     * @param array $columns
     * @param array $insertChunks
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    protected function processInsertChunks(
        string $insertSql,
        array $bindChunks,
        array $columns,
        array $insertChunks
    ): int {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $db */
        $db        = $this->getConnection();
        $affected  = 0;
        foreach ($bindChunks as $k => $chunk) {
            // Use a straight query to bypass the overhead of objects and 100k insert queries.
            /** @var \Zend_Db_Statement_Pdo $res */
            $result = $db->query(
                sprintf(
                    $insertSql,
                    implode('`,`', $columns),
                    implode(',', $chunk)
                ),
                $insertChunks[ $k ]
            );

            $affected += $result->rowCount();
        }

        return $affected;
    }

    /**
     * Turn a two-dimensional array into a one-dimensional one, values only.
     *
     * @param array $inserts
     * @return array
     */
    protected function flattenArray(array $inserts): array
    {
        $flat = [];
        foreach ($inserts as $insert) {
            foreach ($insert as $value) {
                $flat[] = $value;
            }
        }

        return $flat;
    }

    /**
     * Create a bind pattern for each column and value-set in the inserts.
     *
     * @param int $columnCount
     * @param int $insertCount
     * @return array
     */
    protected function makeBindPatterns(int $columnCount, int $insertCount): array
    {
        // Create (?,?,?) bind pattern based on the number of columns.
        $bindPattern = sprintf(
            '(%s)',
            str_pad(
                '',
                ($columnCount * 2) - 1,
                '?,'
            )
        );

        // Fill out an array of bind patterns, with one for each set of inserts.
        $binds = array_fill(
            0,
            $insertCount / $columnCount,
            $bindPattern
        );

        return $binds;
    }

    /**
     * Create SQL insert pattern for the given table.
     *
     * @param string $table
     * @return string
     */
    protected function makeInsertSql(string $table): string
    {
        return 'INSERT INTO `' . $this->getConnection()->getTableName($table) . '` (`%s`) VALUES %s';
    }

    /**
     * Create SQL insert..update pattern based on the update columns (if any).
     *
     * @param array $updateCols
     * @param bool $increment
     * @return string
     */
    protected function makeDuplicateSql(array $updateCols, bool $increment = false)
    {
        if (empty($updateCols)) {
            return '';
        }

        $updates = [];
        foreach ($updateCols as $col) {
            $updates[] = ($increment === true)
                ? sprintf(' `%1$s`=`%1$s` + VALUES(`%1$s`)', $col)
                : sprintf(' `%1$s`=VALUES(`%1$s`)', $col);
        }

        return ' ON DUPLICATE KEY UPDATE' . implode(',', $updates);
    }
}
