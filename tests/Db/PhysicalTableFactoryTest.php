<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use EcomDev\IndexerWrapper\Db\Statement\ShowCreateTable;
use EcomDev\IndexerWrapper\TestDbAdapterFactory;
use PHPUnit\Framework\TestCase;

class PhysicalTableFactoryTest extends TestCase
{
    const TABLE_FIXTURES = [
        'simple_table' => [
            'definition' => [
                'columns' => [
                    'id' => ['type' => 'int', 'size' => '10', 'identity' => true]
                ]
            ],
            'result' => 'CREATE TABLE `table_one` (
                `id` INT(10) NOT NULL AUTO_INCREMENT, 
                PRIMARY KEY (`id`)
            );'
        ],
        'table_with_foreign_key' => [
            'definition' => [
                'columns' => [
                    'id' => ['type' => 'int', 'size' => '10', 'identity' => true],
                    'foreign_id' => ['type' => 'int', 'size' => '10']
                ],
                'foreign_keys' => [
                    'foreign_id' => ['simple_table', 'id']
                ]
            ],
            'result' => 'CREATE TABLE `table_with_index_key` (
                `id` INT(10) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`),
                `foreign_id` INT(10) NOT NULL,
                FOREIGN KEY(`foreign_id`, `simple_table`)
            );'
        ]
    ];

    private $dbAdapter;

    protected function setUp()
    {
        $this->dbAdapter = TestDbAdapterFactory::createTestDbAdapter();
    }


    /**
     * @testWith ["simple_table"]
     *           ["table_with_foreign_key"]
     */
    public function it_creates_simple_table_from_table_definition($tableName)
    {
        $factory = $this->createFactory();

        $factory->createTableFromDefinition($tableName, self::TABLE_FIXTURES[$tableName]['definition']);

        $this->assertTableStructure($tableName, self::TABLE_FIXTURES[$tableName]['result']);
    }

    protected function createFactory(): PhysicalTableFactory
    {
        return new PhysicalTableFactory($this->dbAdapter);
    }

    /**
     * Asserts table structure and ignores white-spaces in response from database
     *
     * @param string $tableName
     * @param string $expectedTableStructure
     */
    private function assertTableStructure($tableName, $expectedTableStructure)
    {
        $this->assertSame(
            $this->stripWhiteSpaces($expectedTableStructure),
            $this->stripWhiteSpaces($this->dbAdapter->execute(new ShowCreateTable($tableName)))
        );
    }

    /**
     * Strips white-spaces (tabs, new-lines, etc) from string
     *
     * @param string $string
     * @return string
     */
    private function stripWhiteSpaces($string)
    {
        return preg_replace('/\s+/', ' ', $string);
    }
}
