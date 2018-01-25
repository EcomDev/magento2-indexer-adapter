<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use EcomDev\DatabaseWrapper\TestUtilities\TestDatabaseFactory;
use EcomDev\DatabaseWrapper\TestUtilities\TestDatabaseAdapter;
use PHPUnit\Framework\TestCase;

class TestDbAdapterTest extends TestCase
{
    /**
     * Pdo connection for test db adapter
     *
     * @var TestDatabaseFactory
     */
    private static $adapterFactory;

    /** @var \PDO */
    private $pdoConnection;

    public static function setUpBeforeClass()
    {
        self::$adapterFactory = new TestDatabaseFactory();
    }

    /** @test */
    public function createsNewDatabaseForTest()
    {
        $adapter = $this->createTestAdapter();

        $testDatabase = $adapter->getDatabaseName();

        $this->assertStringStartsWith(
            'db_unit_test',
            $testDatabase
        );

        $this->assertNotEquals(
            'db_unit_test',
            $testDatabase
        );

        $this->assertContains(
            $testDatabase,
            $this->fetchDatabaseList()
        );


    }

    /** @test */
    public function dropsDestDatabaseAfterDereferenceOfAdapter()
    {
        $adapter = $this->createTestAdapter();

        $dbSchema = $adapter->getDatabaseName();

        unset($adapter);

        $this->assertNotContains($dbSchema, $this->fetchDatabaseList());
    }

    /** @test */
    public function createsMagentoDatabaseConnectionSandboxedInNewSchema()
    {
        $adapter = $this->createTestAdapter();

        $dbSchema = $adapter->getDatabaseName();

        $magentoConnection = $adapter->createConnection();

        $this->assertEquals(
            $dbSchema,
            $magentoConnection->executeQuery('SELECT DATABASE()')->fetchOne()
        );
    }

    /** @test */
    public function cachesCreatedConnections()
    {
        $adapter = $this->createTestAdapter();

        $firstConnection = $adapter->createConnection();

        $this->assertSame(
            $firstConnection,
            $adapter->createConnection()
        );
    }

    /** @test */
    public function cachesCreatedConnectionsByTablePrefix()
    {
        $adapter = $this->createTestAdapter();

        $connectionWithoutPrefix = $adapter->createConnection();

        $prefixedConnection = $adapter->createConnection('prefix_');

        $this->assertNotSame($connectionWithoutPrefix, $prefixedConnection);
    }

    /** @test */
    public function loadsTablesIntoDatabase()
    {
        $adapter = $this->createTestAdapter();

        $definitionBuilder = TableDefinitionBuilder::create();

        $adapter->loadSchema([
            $definitionBuilder
                ->newTable()
                ->withName('table1')
                ->withIdentity('id')
                ->build(),
            $definitionBuilder
                ->newTable()
                ->withName('table2')
                ->withIdentity('id')
                ->build()
        ]);

        $this->assertEquals(
            ['table1', 'table2'],
            $adapter->createConnection()->executeQuery('SHOW TABLES')->fetchCol()
        );
    }

    /** @test */
    public function loadSchemaFileFromEnvironmentConfiguration()
    {
        $adapter = $this->createTestAdapter([
            TableDefinitionBuilder::create()
                ->newTable()
                ->withName('table3')
                ->withIdentity('id')
                ->build(),
            TableDefinitionBuilder::create()
                ->newTable()
                ->withName('table4')
                ->withIdentity('id')
                ->build()
        ]);

        $adapter->loadSchemaFixture();

        $this->assertEquals(
            ['table3', 'table4'],
            $adapter->createConnection()->executeQuery('SHOW TABLES')->fetchCol()
        );
    }

    private function fetchDatabaseList(): array
    {
        $databases = [];

        foreach ($this->pdoConnection->query('SHOW DATABASES') as $row) {
            $databases[] = current($row);
        }

        return $databases;
    }

    public static function tearDownAfterClass()
    {
        self::$adapterFactory  = null;
    }

    /**
     * Creates a test database adapter
     *
     * @param string $databaseNamePrefix
     * @return TestDatabaseAdapter
     */
    private function createTestAdapter(array $schema = []): TestDatabaseAdapter
    {
        $adapter = self::$adapterFactory->createTestDbAdapter($schema);
        $this->pdoConnection = $adapter->getPdoConnection();
        return $adapter;
    }
}
