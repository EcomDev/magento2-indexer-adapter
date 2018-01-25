<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\NameResolver;

/**
 * Fake resolver for various database configurations
 *
 * Replicates behaviour of MagentoNameResolver, but without involving ResourceConnection
 */
class FakeNameResolver implements NameResolver
{
    /**
     * Prefix for table name resolution
     *
     * @var string
     */
    private $tableNamePrefix;

    public function __construct(string $tableNamePrefix = '')
    {
        $this->tableNamePrefix = $tableNamePrefix;
    }

    /**
     * Replicates simplified behaviour of Magento table name resolution
     *
     * @param  string|string[] $tableName
     * @return string
     */
    public function resolveTableName($tableName): string
    {
        if (is_array($tableName)) {
            $tableName = implode('_', $tableName);
        }

        return $this->tableNamePrefix . $tableName;
    }

    /**
     * Replicates behaviour of index name generation via real name resolver
     *
     * Does not take into account index type
     *
     * @param  string|string[] $tableName
     * @param  array $fields
     * @param  string $indexType
     * @return string
     *
     * @SupressWarning(PHPMD.UnusedFormalParameter)
     */
    public function resolveIndexName($tableName, array $fields, string $indexType): string
    {
        return strtoupper($this->resolveTableName($tableName) . '_' . implode('_', $fields));
    }

    /**
     * Combines all passed arguments and resolves table name
     *
     * @param  string|string[] $tableName
     * @param  string $columnName
     * @param  string|string[] $referenceTableName
     * @param  string $referenceColumn
     * @return string
     */
    public function resolveForeignKeyName(
        $tableName,
        string $columnName,
        $referenceTableName,
        string $referenceColumn
    ): string {
        $parts = [
            $this->resolveTableName($tableName),
            $columnName,
            $this->resolveTableName($referenceTableName),
            $referenceColumn
        ];

        return strtoupper(implode('_', $parts));
    }
}
