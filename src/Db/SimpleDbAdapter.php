<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;


use EcomDev\IndexerWrapper\Db\Statement\CurrentDatabase;
use Magento\Framework\DB\Adapter\AdapterInterface;

class SimpleDbAdapter implements DbAdapter
{
    /**
     * Table prefix
     *
     * @var string
     */
    private $tablePrefix;

    /**
     * Database adapter for query execution
     *
     * @var AdapterInterface
     */
    private $databaseAdapter;

    public function __construct(AdapterInterface $databaseAdapter, $tablePrefix = '')
    {
        $this->tablePrefix = $tablePrefix;
        $this->databaseAdapter = $databaseAdapter;
    }

    public function resolveTableName($name)
    {
        return $this->tablePrefix . $name;
    }

    public function execute(Statement $query)
    {
        return $query->execute($this->databaseAdapter, $this);
    }

    public function getSchema(): string
    {
        return $this->execute(new CurrentDatabase());
    }

}
