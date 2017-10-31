<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use EcomDev\IndexerWrapper\Db\Statement\DatabaseList;
use EcomDev\IndexerWrapper\Db\Statement\TableList;
use EcomDev\IndexerWrapper\TestDbAdapterFactory;
use PHPUnit\Framework\TestCase;

class TestDbAdapterTest extends TestCase
{
    /** @test */
    public function delegates_table_resolution_to_simple_adapter()
    {
        $simpleDbAdapter = TestDbAdapterFactory::createSimpleDbAdapter(['table_prefix' => 'prefix_']);

        $adapter = TestDbAdapterFactory::createTestDbAdapter('test_db', $simpleDbAdapter);

        $this->assertSame('prefix_table_1', $adapter->resolveTableName('table_1'));
    }

    /** @test */
    public function creates_new_database_for_test()
    {
        $simpleDbAdapter = TestDbAdapterFactory::createSimpleDbAdapter();

        $adapter = TestDbAdapterFactory::createTestDbAdapter('test_db', $simpleDbAdapter);

        $this->assertStringStartsWith(
            'test_db',
            $adapter->getSchema()
        );
    }

    /** @test */
    public function drops_test_database_after_dereference_of_test_adapter()
    {
        $simpleDbAdapter = TestDbAdapterFactory::createSimpleDbAdapter();

        $adapter = TestDbAdapterFactory::createTestDbAdapter('test_db', $simpleDbAdapter);
        unset($adapter);

        $this->assertEquals('information_schema', $simpleDbAdapter->getSchema());
    }

    /** @test */
    public function drops_test_database_after_dereference_of_adapter()
    {
        $simpleDbAdapter = TestDbAdapterFactory::createSimpleDbAdapter();

        $adapter = TestDbAdapterFactory::createTestDbAdapter('test_db', $simpleDbAdapter);
        $dbSchema = $adapter->getSchema();

        unset($adapter);

        $this->assertNotContains($dbSchema, $simpleDbAdapter->execute(new DatabaseList()));
    }

    /** @test */
    public function loads_fixture_file_into_database()
    {
        $adapter = TestDbAdapterFactory::createTestDbAdapter();

        $adapter->loadFixture([
            'CREATE TABLE table1 SELECT 1 as field1',
            'CREATE TABLE table2 SELECT 2 as field2',
        ]);

        $this->assertEquals(['table1', 'table2'], $adapter->execute(new TableList()));
    }

}
