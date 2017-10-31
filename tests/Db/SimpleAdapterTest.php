<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use EcomDev\IndexerWrapper\TestDbAdapterFactory;
use PHPUnit\Framework\TestCase;

/**
 * Test for simple adapter implementation
 *
 */
class SimpleAdapterTest extends TestCase
{
    /** @test */
    public function given_config_has_no_prefix_resolves_table_name_as_is()
    {
        $adappter = TestDbAdapterFactory::createSimpleDbAdapter();
        $this->assertSame('table1', $adappter->resolveTableName('table1'));
    }

    /** @test */
    public function given_prefix_in_config_resolves_table_name_with_prefix_in_front()
    {
        $adapter = TestDbAdapterFactory::createSimpleDbAdapter(['table_prefix' => 'prefix_']);

        $this->assertSame('prefix_table_1', $adapter->resolveTableName('table_1'));
    }

    /** @test */
    public function builds_query_with_internal_adapter()
    {
        $adapter = TestDbAdapterFactory::createSimpleDbAdapter();
        $this->assertSame(
            ['calculation' => '1'],
            $adapter->execute(TestDbAdapterFactory::createSingleRowQuery('SELECT 1 as calculation'))
        );
    }

    /** @test */
    public function returns_currently_selected_schema()
    {
        $adapter = TestDbAdapterFactory::createSimpleDbAdapter();
        $this->assertSame('information_schema', $adapter->getSchema());
    }
}
