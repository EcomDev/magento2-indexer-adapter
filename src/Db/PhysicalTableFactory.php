<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;


use EcomDev\IndexerWrapper\Db\Statement\TableDefinition;

class PhysicalTableFactory
{
    /**
     * Database adapter
     *
     * @var DbAdapter
     */
    private $dbAdapter;

    public function __construct(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function createTableFromDefinition($name, array $definition = [])
    {
        $tableObject = $this->dbAdapter->execute(
            new TableDefinition($name, $definition)
        );
    }
}
