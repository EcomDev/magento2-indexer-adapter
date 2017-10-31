<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;

use EcomDev\IndexerWrapper\LightDbTestCase;
use Magento\Framework\DB\Ddl\Table;

class TableDefinitionTest extends LightDbTestCase
{
    const COLUMNS = [
        'integer_column' => [
            'name' => 'integer_column',
            'size' => 10
        ],
        'short_text_column' => [
            'name' => 'short_text',
            'size' => 255
        ],
        'medium_text_column' => [
            'short_text_col'
        ]
    ];

    /** @var TableDefinitionBuilder */
    private $builder;

    protected function setUp()
    {
        $this->builder = new TableDefinitionBuilder();
    }

    /** @test */
    public function creates_table_object_with_name()
    {
        $tableDefinition = $this->builder->newTable()
            ->withName('my_table_name')
            ->build();

        $table = $this->execute($tableDefinition);

        $this->assertEquals('my_table_name', $table->getName());
    }

    /** @test */
    public function creates_table_object_with_prefixed_table_name()
    {
        $tableDefinition = $this->builder->newTable()
            ->withName('my_table_name')
            ->build();

        $table = $this->execute($tableDefinition, ['table_prefix' => 'prefix_']);

        $this->assertEquals('prefix_my_table_name', $table->getName());
    }

    /** @test */
    public function creates_table_with_columns_without_size()
    {
        $tableDefinition = $this->builder->newTable()
            ->withName('example_table')
            ->withColumn('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('small_column', TableDefinition::TYPE_SMALLINT)
            ->withColumn('big_column', TableDefinition::TYPE_BIGINT)
            ->withColumn('unsigned_int_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->withColumn('datetime_column', TableDefinition::TYPE_DATETIME)
            ->withColumn('date_column', TableDefinition::TYPE_DATE)
            ->build()
        ;

        $this->assertTableColumns(
            [
                ['name' => 'int_column', 'type' => TableDefinition::TYPE_INTEGER],
                ['name' => 'small_column', 'type' => TableDefinition::TYPE_SMALLINT],
                ['name' => 'big_column', 'type' => TableDefinition::TYPE_BIGINT],
                ['name' => 'unsigned_int_column', 'type' => TableDefinition::TYPE_INTEGER, 'unsigned' => true],
                ['name' => 'datetime_column', 'type' => TableDefinition::TYPE_DATETIME],
                ['name' => 'date_column', 'type' => TableDefinition::TYPE_DATE],
            ],
            $this->execute($tableDefinition)
        );
    }

    /** @test */
    public function creates_table_with_columns_nullable_collumns()
    {
        $tableDefinition = $this->builder->newTable()
            ->withName('example_table')
            ->withColumn('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('small_column', TableDefinition::TYPE_SMALLINT)
            ->withColumn('big_column', TableDefinition::TYPE_BIGINT)
            ->withColumn('datetime_column', TableDefinition::TYPE_DATETIME)
            ->withColumn('date_column', TableDefinition::TYPE_DATE)
            ->build()
        ;

        $this->assertTableColumns(
            [
                ['name' => 'int_column', 'type' => TableDefinition::TYPE_INTEGER],
                ['name' => 'small_column', 'type' => TableDefinition::TYPE_SMALLINT],
                ['name' => 'big_column', 'type' => TableDefinition::TYPE_BIGINT],
                ['name' => 'datetime_column', 'type' => TableDefinition::TYPE_DATETIME],
                ['name' => 'date_column', 'type' => TableDefinition::TYPE_DATE],
            ],
            $this->execute($tableDefinition)
        );
    }


    /** @test */
    public function creates_table_with_columns_with_size()
    {
        $tableDefinition = $this->builder->newTable()
            ->withName('example_table')
            ->withColumn('varchar_column', TableDefinition::TYPE_TEXT, 255)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, '128k')
            ->withColumn('decimal_column', TableDefinition::TYPE_TEXT, [12, 4])
            ->build()
        ;

        $this->assertTableColumns(
            [
                ['name' => 'varchar_column', 'type' => TableDefinition::TYPE_TEXT, 'length' => 255],
                ['name' => 'text_column', 'type' => TableDefinition::TYPE_TEXT, 'length' => '128k'],
                ['name' => 'decimal_column', 'type' => TableDefinition::TYPE_TEXT, 'precision' => 12, 'scale' => 4],
            ],
            $this->execute($tableDefinition)
        );
    }


    private function assertTableColumns(array $columns, Table $table)
    {
        $actualColumns = $table->getColumns();

        foreach ($columns as $column) {
            $columnKey = strtoupper($column['name']);
            $this->assertArrayHasKey(
                $columnKey,
                $actualColumns
            );

            $this->assertArraySubset(
                [
                    'COLUMN_NAME' => $column['name'],
                    'COLUMN_TYPE' => $column['type'],
                    'DATA_TYPE' => $column['type'],
                    'DEFAULT' => $column['default'] ?? false,
                    'NULLABLE' => $column['nullable'] ?? false,
                    'LENGTH' => $column['length'] ?? null,
                    'SCALE' => $column['scale'] ?? null,
                    'PRECISION' => $column['precision'] ?? null,
                    'UNSIGNED' => $column['unsigned'] ?? false,
                    'PRIMARY' => $column['primary'] ?? false,
                    'IDENTITY' => $column['identity'] ?? false
                ],
                $actualColumns[$columnKey],
                sprintf('Table column "%s" does not match expected definition', $column['name'])
            );
        }
    }
}
