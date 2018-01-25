<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use EcomDev\DatabaseWrapper\Db\TableDefinition;
use Magento\Framework\App\ResourceConnection;
use PHPUnit\DbUnit\Database\DefaultConnection;

class TestDatabaseAdapter
{
    /**
     * @var \PDO
     */
    private $pdoConnection;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var TestConfiguration
     */
    private $configuration;
    /**
     * @var TestDatabaseConnectionFactory
     */
    private $connectionFactory;

    /**
 * @var DatabaseConnection[]
*/
    private $connections = [];
    /**
     * @var TestResourceConnectionFactory
     */
    private $resourceConnectionFactory;

    public function __construct(
        TestConfiguration $configuration,
        TestDatabaseConnectionFactory $connectionFactory,
        TestResourceConnectionFactory $resourceConnectionFactory
    ) {
        $this->configuration = $configuration;
        $this->connectionFactory = $connectionFactory;
        $this->resourceConnectionFactory = $resourceConnectionFactory;
    }

    public function getDatabaseName(): string
    {
        if (empty($this->databaseName)) {
            $this->databaseName = $this->initializeTestDatabase();
        }

        return $this->databaseName;
    }

    public function __destruct()
    {
        if ($this->databaseName) {
            $this->dropDatabase($this->databaseName);
        }
    }

    private function initializeTestDatabase()
    {
        $dbName = uniqid($this->configuration->getConnection()['db_name_prefix']);
        $this->createDatabase($dbName);
        return $dbName;
    }

    public function getPdoConnection()
    {
        if (!$this->pdoConnection) {
            $this->pdoConnection = $this->createPdoConnection();
        }

        return $this->pdoConnection;
    }

    public function createDBUnitConnection()
    {
        return new DefaultConnection($this->createPdoConnection($this->getDatabaseName()), $this->getDatabaseName());
    }

    private function createDatabase(string $dbName)
    {
        $this->getPdoConnection()->query(sprintf('CREATE DATABASE %s', $dbName));
    }

    private function dropDatabase(string $dbName)
    {
        $this->getPdoConnection()->query(sprintf('DROP DATABASE %s', $dbName));
    }

    /**
     * Loads database schema from table definitions
     *
     * @param TableDefinition[] $definitions
     */
    public function loadSchema(array $definitions)
    {
        $connection = $this->createConnection();
        foreach ($definitions as $definition) {
            $connection->createTable($definition);
        }
    }

    /**
     * @return
     */
    public function loadSchemaFixture()
    {
        $this->loadSchema($this->configuration->getSchema());
    }

    /**
     * @return \PDO
     */
    private function createPdoConnection(string $databaseName = null): \PDO
    {
        $connectionConfiguration = $this->configuration->getConnection();

        $dsnOptions = [
            'host' => $connectionConfiguration['host'],
            'charset' => 'UTF8'
        ];

        if ($databaseName !== null) {
            $dsnOptions['dbname'] = $databaseName;
        }

        $connection = new \PDO(
            sprintf('mysql:%s', http_build_query($dsnOptions, '', ';')),
            $connectionConfiguration['username'],
            $connectionConfiguration['password']
        );

        return $connection;
    }

    public function createResourceConnection(string $tablePrefix = ''): ResourceConnection
    {
        return $this->resourceConnectionFactory->createResourceConnection(
            $this->getDatabaseName(),
            $tablePrefix
        );
    }

    public function createConnection(string $tablePrefix = ''): DatabaseConnection
    {
        if (!isset($this->connections[$tablePrefix])) {
            $this->connections[$tablePrefix] = $this->connectionFactory->createConnection(
                $this->createResourceConnection($tablePrefix)
            );
        }

        return $this->connections[$tablePrefix];
    }
}
