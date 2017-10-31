<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use EcomDev\IndexerWrapper\Db\Statement\Callback;
use Magento\Framework\DB\Adapter\AdapterInterface;

class TestDbAdapter implements DbAdapter
{
    /**
     * Original database adapter
     *
     * @var DbAdapter
     */
    private $dbAdapter;

    /**
     * Database name in test
     *
     * @var string
     */
    private $dbName;

    /**
     * Original database in connection
     *
     * @var string
     */
    private $originalDbName;


    public function __construct(DbAdapter $dbAdapter, $prefix)
    {
        $this->dbAdapter = $dbAdapter;
        $this->dbName = $this->initializeTestDatabase($prefix);
    }

    public function resolveTableName($name)
    {
        return $this->dbAdapter->resolveTableName($name);
    }

    public function execute(Statement $query)
    {
        return $this->dbAdapter->execute($query);
    }

    public function getSchema(): string
    {
        return $this->dbName;
    }

    public function __destruct()
    {
        $this->changeDatabase($this->originalDbName);
        $this->dropDatabase($this->dbName);
    }

    private function initializeTestDatabase($prefix)
    {

        $this->originalDbName = $this->extractCurrentDatabaseName();

        $dbName = uniqid($prefix);

        $this->createDatabase($dbName);
        $this->changeDatabase($dbName);

        return $dbName;
    }

    private function changeDatabase(string $dbName)
    {
        $this->execute(
            $this->createQuery(sprintf('USE %s', $dbName))
        );
    }

    private function createDatabase(string $dbName)
    {
        $this->execute(
            $this->createQuery(sprintf('CREATE DATABASE %s', $dbName))
        );
    }

    private function dropDatabase(string $dbName)
    {
        $this->execute(
            $this->createQuery(sprintf('DROP DATABASE %s', $dbName))
        );
    }

    private function extractCurrentDatabaseName(): string
    {
        return $this->execute($this->createQuery(sprintf('SELECT DATABASE();')))->fetchColumn();
    }

    private function createQuery($query): Callback
    {
        return new Callback(function (AdapterInterface $adapter) use ($query) {
            return $adapter->query($query);
        });
    }

    public function loadFixture($fixture)
    {
        foreach ($fixture as $statement) {
            $this->execute($this->createQuery($statement));
        }
    }

}
