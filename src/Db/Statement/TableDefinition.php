<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;


use EcomDev\IndexerWrapper\Db\DbAdapter;
use EcomDev\IndexerWrapper\Db\Statement;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class TableDefinition implements Statement
{
    const TYPE_SMALLINT = Table::TYPE_SMALLINT;
    const TYPE_INTEGER = Table::TYPE_INTEGER;
    const TYPE_BIGINT = Table::TYPE_BIGINT;
    const TYPE_TEXT = Table::TYPE_TEXT;
    const TYPE_DECIMAL = Table::TYPE_DECIMAL;
    const TYPE_DATETIME = Table::TYPE_DATETIME;
    const TYPE_DATE = Table::TYPE_DATE;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $columns;


    public function __construct($tableName, array $columns = [])
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
    }

    public function execute(AdapterInterface $magentoAdapter, DbAdapter $dbAdapter)
    {
        $tableObject = $magentoAdapter->newTable($dbAdapter->resolveTableName($this->tableName));
        foreach ($this->columns as $column) {
            $tableObject->addColumn($column['name'], $column['type'], $column['size'], $column['options']);
        }

        return $tableObject;
    }

}
