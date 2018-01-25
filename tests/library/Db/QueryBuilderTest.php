<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    /** @var QueryBuilder */
    private $queryBuilder;

    protected function setUp()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /** @test */
    public function createsSelectWithColumnsFromTable()
    {
        $this->markTestSkipped('Not implemented yet');
        $query = $this->queryBuilder->newQuery()
            ->fromTable('table_one')
            ->withColumn('column_one')
            ->withColumn('column_two')
            ->build();

         $this->assertEquals(
             new Query(
                 [
                     'source' => [
                         'table_one' => [
                            'type' => 'from',
                            'table' => 'table_one',
                            'condition' => []
                        ]
                     ],
                     'columns' => []
                 ]
             ),
            $query
         );
    }
}
