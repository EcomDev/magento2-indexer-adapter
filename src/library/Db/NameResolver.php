<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

interface NameResolver
{
    /**
     * Resolves name of a table
     *
     * @param  string|string[] $tableName
     * @return string
     */
    public function resolveTableName($tableName): string;

    /**
     * Resolves index name for a table
     *
     * @param  string|string[] $tableName
     * @param  string[] $fields
     * @param  string $indexType
     * @return string
     */
    public function resolveIndexName($tableName, array $fields, string $indexType): string;

    /**
     * Resolves table foreign key name
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
    ): string;
}
