<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use EcomDev\DatabaseWrapper\Db\DbAdapter;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

final class TableDefinition
{
    const TYPE_SMALLINT = Table::TYPE_SMALLINT;
    const TYPE_INTEGER = Table::TYPE_INTEGER;
    const TYPE_BIGINT = Table::TYPE_BIGINT;
    const TYPE_TEXT = Table::TYPE_TEXT;
    const TYPE_DECIMAL = Table::TYPE_DECIMAL;
    const TYPE_DATETIME = Table::TYPE_DATETIME;
    const TYPE_DATE = Table::TYPE_DATE;

    const INDEX_TYPE_FULLTEXT = AdapterInterface::INDEX_TYPE_FULLTEXT;
    const INDEX_TYPE_INDEX = AdapterInterface::INDEX_TYPE_INDEX;
    const INDEX_TYPE_UNIQUE = AdapterInterface::INDEX_TYPE_UNIQUE;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $indexes;

    /**
     * @var array
     */
    private $foreignKeys;

    /**
     * @var array
     */
    private $options;


    public function __construct(
        $tableName,
        array $columns = [],
        array $indexes = [],
        array $foreignKeys = [],
        array $options = []
    ) {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->indexes = $indexes;
        $this->foreignKeys = $foreignKeys;
        $this->options = $options;
    }

    /**
     * Configures Magento DDL table object
     *
     * @param Table $table
     * @param NameResolver $nameResolver
     *
     * @return Table
     */
    public function configure(Table $table, NameResolver $nameResolver)
    {
        $table->setName(
            $nameResolver->resolveTableName($this->tableName ?? uniqid('anonymous_table_'))
        );

        $this->configureTableColumns($table);
        $this->configureTableKeys($table, $nameResolver);
        $this->configureTableForeignKeys($table, $nameResolver);
        $this->configureTableOptions($table);

        return $table;
    }

    /**
     * Copies current table definition into builder
     *
     * @param TableDefinitionBuilder $builder
     *
     * @return TableDefinitionBuilder
     */
    public function copy(TableDefinitionBuilder $builder): TableDefinitionBuilder
    {
        $builder = $builder->withName($this->tableName);
        $builder = $this->copyColumns($builder);
        $builder = $this->copyIndexes($builder);
        $builder = $this->copyForeignKeys($builder);
        $builder = $this->copyOptions($builder);
        return $builder;
    }

    private function configureTableColumns($tableObject)
    {
        foreach ($this->columns as $columnName => $column) {
            $tableObject->addColumn($columnName, $column['type'], $column['size'], $column['options']);
        }
    }

    private function configureTableKeys(Table $tableObject, NameResolver $nameResolver)
    {
        foreach ($this->indexes as list($columns, $type)) {
            $tableObject->addIndex(
                $nameResolver->resolveIndexName($this->tableName, $columns, $type),
                $columns,
                ['type' => $type]
            );
        }
    }

    private function configureTableForeignKeys(Table $tableObject, NameResolver $nameResolver)
    {
        foreach ($this->foreignKeys as list($column, $referenceTable, $referenceColumn, $action)) {
            $tableObject->addForeignKey(
                $nameResolver->resolveForeignKeyName(
                    $this->tableName,
                    $column,
                    $referenceTable,
                    $referenceColumn
                ),
                $column,
                $nameResolver->resolveTableName(
                    $referenceTable
                ),
                $referenceColumn,
                $action
            );
        }
    }

    private function configureTableOptions(Table $tableObject)
    {
        foreach ($this->options as $optionName => $optionValue) {
            $tableObject->setOption($optionName, $optionValue);
        }
    }

    private function copyColumns(TableDefinitionBuilder $builder): TableDefinitionBuilder
    {
        foreach ($this->columns as $columnName => $column) {
            $builder = $builder->withColumn($columnName, $column['type'], $column['size'], $column['options']);
        }

        return $builder;
    }

    private function copyIndexes(TableDefinitionBuilder $builder): TableDefinitionBuilder
    {
        foreach ($this->indexes as list($columns, $type)) {
            $builder = $builder->withKey($type, $columns);
        }

        return $builder;
    }

    private function copyForeignKeys(TableDefinitionBuilder $builder): TableDefinitionBuilder
    {
        foreach ($this->foreignKeys as $foreignKey) {
            $builder = $builder->withForeignKey(...$foreignKey);
        }

        return $builder;
    }

    private function copyOptions(TableDefinitionBuilder $builder): TableDefinitionBuilder
    {
        foreach ($this->options as $optionName => $optionValue) {
            $builder = $builder->withOption($optionName, $optionValue);
        }

        return $builder;
    }
}
