<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db\DatabaseConnection;


use EcomDev\DatabaseWrapper\Db\QueryBuilder;
use EcomDev\DatabaseWrapper\TestUtilities\FixtureBasedDatabaseTestCase;

/**
 * @schema schema/query.php
 */
class QueryExecutionTest extends FixtureBasedDatabaseTestCase
{
    /** @var QueryBuilder */
    private $queryBuilder;

    protected function setUp()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'product' => [
                ['sku' => 'product_2', 'position' => 2],
                ['sku' => 'product_1', 'position' => 1],
                ['sku' => 'product_3', 'position' => 3],
            ]
        ]);
    }

    /** @test */
    public function readsWholeTableDataAsItWasInserted()
    {
        $this->assertEquals(
            [
                ['sku' => 'product_2', 'position' => 2],
                ['sku' => 'product_1', 'position' => 1],
                ['sku' => 'product_3', 'position' => 3],
            ],
            $this->createConnection()->executeQuery(
                $this->queryBuilder->newQuery()
                    ->fromTable('product')
                    ->withColumn('sku')
                    ->withColumn('position')
                    ->build()
            )->fetchAll()
        );
    }

    /** @test */
    public function readsSingleColumnFromTableData()
    {
        $this->assertEquals(
            [
                ['sku' => 'product_2'],
                ['sku' => 'product_1'],
                ['sku' => 'product_3'],
            ],
            $this->createConnection()->executeQuery('SELECT sku FROM product')->fetchAll()
        );
    }

    /** @test */
    public function readsSingleColumnFromTableOrderedByAnotherColumn()
    {
        $this->assertEquals(
            [
                ['sku' => 'product_1'],
                ['sku' => 'product_2'],
                ['sku' => 'product_3'],
            ],
            $this->createConnection()->executeQuery('SELECT sku FROM product ORDER BY position')->fetchAll()
        );
    }
}
