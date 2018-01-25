<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use EcomDev\DatabaseWrapper\TestUtilities\TestPdoStatement;
use PHPUnit\Framework\TestCase;

/**
 * Magento query result set
 *
 */
class MagentoQueryResultTest extends TestCase
{
    /** @var QueryResult */
    private $queryResult;

    protected function setUp()
    {
        $this->queryResult = new QueryResult(
            TestPdoStatement::createFromArray([
                ['field1' => 'value1.1', 'field2' => 'value2.1'],
                ['field1' => 'value1.2', 'field2' => 'value2.2'],
            ])
        );
    }

    /** @test */
    public function iteratesOverStatementOnIteratingItself()
    {
        $this->assertEquals(
            [
                ['field1' => 'value1.1', 'field2' => 'value2.1'],
                ['field1' => 'value1.2', 'field2' => 'value2.2'],
            ],
            iterator_to_array($this->queryResult)
        );
    }

    /** @test */
    public function fetchesFirstValueInResultSetByDefault()
    {
        $this->assertEquals('value1.1', $this->queryResult->fetchOne());
    }

    /**
     * @testWith
     *    [0, "value1.1"]
     *    [1, "value2.1"]
     */
    public function fetchesAnyColumnValueInRow($columnIndex, $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->queryResult->fetchOne($columnIndex));
    }

    /** @test */
    public function fetchesFirstColumnValuesOfAllResultSet()
    {
        $this->assertEquals(['value1.1', 'value1.2'], $this->queryResult->fetchCol());
    }

    /** @test */
    public function fetchesColumnValuesOfAllResultSetByColumnIndex()
    {
        $this->assertEquals(['value2.1', 'value2.2'], $this->queryResult->fetchCol(1));
    }

    /** @test */
    public function fetchesAssociativePairs()
    {
        $this->assertEquals(
            [
                'value1.1' => 'value2.1',
                'value1.2' => 'value2.2',
            ],
            $this->queryResult->fetchPairs()
        );
    }

    /** @test */
    public function fetchesResultSetKeyedByAssociativeKey()
    {
        $this->assertEquals(
            [
                'value1.1' => ['field1' => 'value1.1', 'field2' => 'value2.1'],
                'value1.2' => ['field1' => 'value1.2', 'field2' => 'value2.2'],
            ],
            $this->queryResult->fetchAssoc()
        );
    }

    /** @test */
    public function fetchesCompleteResultSetAsArray()
    {
        $this->assertEquals(
            [
                ['field1' => 'value1.1', 'field2' => 'value2.1'],
                ['field1' => 'value1.2', 'field2' => 'value2.2'],
            ],
            $this->queryResult->fetchAll()
        );
    }
}
