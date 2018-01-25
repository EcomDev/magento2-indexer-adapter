<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use Magento\Framework\App\ResourceConnection;

class MagentoNameResolver implements NameResolver
{


    /**
     * Resource connection
     *
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Magento name resolver adapter
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function resolveTableName($tableName): string
    {
        return $this->resourceConnection->getTableName($tableName);
    }

    public function resolveIndexName($tableName, array $fields, string $indexType): string
    {
        return $this->resourceConnection->getIdxName($tableName, $fields, $indexType);
    }

    public function resolveForeignKeyName(
        $tableName,
        string $columnName,
        $referenceTableName,
        string $referenceColumn
    ): string {
        return $this->resourceConnection->getFkName($tableName, $columnName, $referenceTableName, $referenceColumn);
    }
}
