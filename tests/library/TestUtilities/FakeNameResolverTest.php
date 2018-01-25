<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;


use PHPUnit\Framework\TestCase;

class FakeNameResolverTest extends TestCase
{
    /** @test */
    public function prefixesTableNameWithGivenPrefix()
    {
        $nameResolver = new FakeNameResolver('prefix_');

        $this->assertEquals('prefix_table', $nameResolver->resolveTableName('table'));
    }

    /** @test */
    public function combinesTableNameArrayIntoSingleString()
    {
        $nameResolver = new FakeNameResolver();

        $this->assertEquals('table_name', $nameResolver->resolveTableName(['table', 'name']));
    }

    /**
     * @testWith
     *      ["table_name"]
     *      [["table","name"]]
     */
    public function usesResolvedTableNameForIndexName($table)
    {
        $nameResolver = new FakeNameResolver('prefix_');

        $this->assertEquals(
            'PREFIX_TABLE_NAME_COLUMN_ONE_COLUMN_TWO',
            $nameResolver->resolveIndexName($table, ['column_one', 'column_two'], 'unique')
        );
    }

    /**
     * @testWith
     *      ["table_name", "reference_table_name"]
     *      [["table","name"], ["reference_table", "name"]]
     *      [["table","name"], ["reference", "table_name"]]
     */
    public function usesResolvedTableNameForForeignKeyName($table, $referenceTable)
    {
        $nameResolver = new FakeNameResolver('prefix_');

        $this->assertEquals(
            'PREFIX_TABLE_NAME_COLUMN_ONE_PREFIX_REFERENCE_TABLE_NAME_COLUMN_TWO',
            $nameResolver->resolveForeignKeyName(
                $table, 'column_one', $referenceTable, 'column_two'
            )
        );
    }
}
