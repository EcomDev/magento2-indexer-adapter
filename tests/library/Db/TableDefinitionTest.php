<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use EcomDev\DatabaseWrapper\TestUtilities\FakeNameResolver;
use Magento\Framework\DB\Ddl\Table;
use PHPUnit\Framework\TestCase;

class TableDefinitionTest extends TestCase
{
    /** @test */
    public function createsTableObjectWithName()
    {
        $tableDefinition = $this->createBuilder()
            ->withName('my_table_name')
            ->build();

        $table = $this->configureTableDefinition($tableDefinition);

        $this->assertEquals('my_table_name', $table->getName());
    }

    /** @test */
    public function createsTableObjectWithPrefixedName()
    {
        $tableDefinition = $this->createBuilder()
            ->withName('my_table_name')
            ->build();

        $table = $this->configureTableDefinition($tableDefinition, 'prefix_');

        $this->assertEquals('prefix_my_table_name', $table->getName());
    }



    /** @test */
    public function createsTableObjectWithColumnsWithoutSize()
    {
        $tableDefinition = $this->createBuilder()
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumnWithoutSize('small_column', TableDefinition::TYPE_SMALLINT)
            ->withColumnWithoutSize('big_column', TableDefinition::TYPE_BIGINT)
            ->withColumnWithoutSize('unsigned_int_column', TableDefinition::TYPE_INTEGER, ['unsigned' => true])
            ->withColumnWithoutSize('datetime_column', TableDefinition::TYPE_DATETIME)
            ->withColumnWithoutSize('date_column', TableDefinition::TYPE_DATE)
            ->build()
        ;

        $this->assertTableColumns(
            [
                'int_column' => ['type' => TableDefinition::TYPE_INTEGER],
                'small_column' => ['type' => TableDefinition::TYPE_SMALLINT],
                'big_column' => ['type' => TableDefinition::TYPE_BIGINT],
                'unsigned_int_column' => ['type' => TableDefinition::TYPE_INTEGER, 'unsigned' => true],
                'datetime_column' => ['type' => TableDefinition::TYPE_DATETIME],
                'date_column' => ['type' => TableDefinition::TYPE_DATE],
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectWithSizeColumns()
    {
        $tableDefinition = $this->createBuilder()
            ->withColumn('varchar_column', TableDefinition::TYPE_TEXT, 255)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, '128k')
            ->withColumn('decimal_column', TableDefinition::TYPE_DECIMAL, [12, 4])
            ->build()
        ;

        $this->assertTableColumns(
            [
                'varchar_column' => ['type' => TableDefinition::TYPE_TEXT, 'length' => 255],
                'text_column' => ['type' => TableDefinition::TYPE_TEXT, 'length' => '128k'],
                'decimal_column' => ['type' => TableDefinition::TYPE_DECIMAL, 'precision' => 12, 'scale' => 4],
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectWithNullableColumns()
    {
        $tableDefinition = $this->createBuilder()
            ->withColumn('int_column', TableDefinition::TYPE_INTEGER, null, ['nullable' => true])
            ->withColumn('datetime_column', TableDefinition::TYPE_DATETIME, null, ['nullable' => true])
            ->build()
        ;

        $this->assertTableColumns(
            [
                'int_column' => ['type' => TableDefinition::TYPE_INTEGER, 'nullable' => true],
                'datetime_column' => ['type' => TableDefinition::TYPE_DATETIME, 'nullable' => true],
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectWithColumnThatHaveDefaultValue()
    {
        $tableDefinition = $this->createBuilder()
            ->withColumn('int_column', TableDefinition::TYPE_INTEGER, null, ['default' => 99])
            ->build()
        ;

        $this->assertTableColumns(
            [
                'int_column' => ['type' => TableDefinition::TYPE_INTEGER, 'default' => 99]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }


    /** @test */
    public function createsTableObjectWithForeignKeyWithoutAction()
    {

        $tableDefinition = $this->createBuilder()
            ->withName('example_table')
            ->withColumn('foreign_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->withForeignKey('foreign_column', 'other_table', 'other_primary_column')
            ->build()
        ;

        $this->assertTableForeignKeys(
            [
                'EXAMPLE_TABLE_FOREIGN_COLUMN_OTHER_TABLE_OTHER_PRIMARY_COLUMN' => [
                    'foreign_column', 'other_table', 'other_primary_column', 'NO ACTION'
                ]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectWithForeignKeyWithCascadeAction()
    {

        $tableDefinition = $this->createBuilder()
            ->withName('example_table')
            ->withColumn('foreign_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->withForeignKey('foreign_column', 'other_table', 'other_primary_column', 'CASCADE')
            ->build()
        ;

        $this->assertTableForeignKeys(
            [
                'EXAMPLE_TABLE_FOREIGN_COLUMN_OTHER_TABLE_OTHER_PRIMARY_COLUMN' => [
                    'foreign_column', 'other_table', 'other_primary_column', 'CASCADE'
                ]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectMultipleIndexesOnExistingColumns()
    {
        $tableDefinition = $this->createBuilder()
            ->withName('example_table')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withIndex('int_column')
            ->withIndex('int_column', 'text_column')
            ->build()
        ;

        $this->assertTableIndexes(
            [
                'EXAMPLE_TABLE_INT_COLUMN' => ['index', ['int_column']],
                'EXAMPLE_TABLE_INT_COLUMN_TEXT_COLUMN' => ['index', ['int_column', 'text_column']]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectUniqueIndexOnExistingColumns()
    {
        $tableDefinition = $this->createBuilder()
            ->withName('example_table')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withUnique('int_column', 'text_column')
            ->build()
        ;


        $this->assertTableIndexes(
            [
                'EXAMPLE_TABLE_INT_COLUMN_TEXT_COLUMN' => ['unique', ['int_column', 'text_column']]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectFulltextIndex()
    {
        $tableDefinition = $this->createBuilder()
            ->withName('example_table')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withFulltext('text_column')
            ->build()
        ;

        $this->assertTableIndexes(
            [
                'EXAMPLE_TABLE_TEXT_COLUMN' => ['fulltext', ['text_column']]
            ],
            $this->configureTableDefinition($tableDefinition)
        );
    }

    /** @test */
    public function createsTableObjectWithUniqueNameOnEveryCallWhenNoNameIsProvided()
    {
        $tableDefinition = $this->createBuilder()
            ->withIdentity('id')
            ->build();

        $firstTable = $this->configureTableDefinition($tableDefinition);
        $secondTable = $this->configureTableDefinition($tableDefinition);

        $this->assertStringStartsWith(
            'anonymous_table_',
            $firstTable->getName(),
            'Table name does not start with anonymous_table_ prefix'
        );

        $this->assertStringStartsWith(
            'anonymous_table_',
            $secondTable->getName(),
            'Table name does not start with anonymous_table_ prefix'
        );

        $this->assertNotEquals(
            $firstTable->getName(),
            $secondTable->getName(),
            'Table name must be unique on every definition processing'
        );
    }

    /** @test */
    public function createsTableObjectWithMemoryEngine()
    {
        $tableDefinition = $this->createBuilder()
            ->withIdentity('id')
            ->withOption('type', 'MEMORY')
            ->build();

        /** @var Table $table */
        $table = $this->configureTableDefinition($tableDefinition);

        $this->assertEquals('MEMORY', $table->getOption('type'));
    }

    /** @test */
    public function createsTableObjectWithCustomAutoIncrementValue()
    {
        $tableDefinition = $this->createBuilder()
            ->withIdentity('id')
            ->withOption('auto_increment', 999)
            ->build();

        /** @var Table $table */
        $table = $this->configureTableDefinition($tableDefinition);

        $this->assertEquals(999, $table->getOption('auto_increment'));
    }


    private function assertTableColumns(array $expectedColumns, Table $table)
    {
        $actualColumns = $table->getColumns();
        $this->assertEquals(
            array_map('strtoupper', array_keys($expectedColumns)),
            array_keys($actualColumns),
            'List of columns do not match'
        );

        foreach ($expectedColumns as $columnName => $expectedColumn) {
            $columnKey = strtoupper($columnName);

            $expectedDefinition = $this->expectedColumnDefinition($columnName, $expectedColumn);

            $actualDefinition = array_intersect_key($actualColumns[$columnKey], $expectedDefinition);

            $this->assertEquals(
                $expectedDefinition,
                $actualDefinition,
                sprintf('Table column "%s" does not match expected definition', $columnName)
            );
        }
    }

    private function assertTableIndexes(array $expectedIndexes, Table $table)
    {
        $actualIndexes = $table->getIndexes();
        $this->assertEquals(array_keys($expectedIndexes), array_keys($actualIndexes), 'List of indexes is different');

        foreach ($expectedIndexes as $expectedIndexName => $expectedIndex) {
            $expectedIndexDefinition = $this->expectedIndexDefinition($expectedIndexName, ...$expectedIndex);

            $this->assertEquals(
                $expectedIndexDefinition,
                $actualIndexes[$expectedIndexName],
                sprintf('Index structure is different for %s', $expectedIndexName)
            );
        }
    }

    private function assertTableForeignKeys(array $expectedIndexes, Table $table)
    {
        $actualIndexes = $table->getForeignKeys();
        $this->assertEquals(array_keys($expectedIndexes), array_keys($actualIndexes), 'List of indexes is different');

        foreach ($expectedIndexes as $expectedIndexName => $expectedForeignKey) {

            $expectedForeignKeyDefinition = $this->expectedForeignKeyDefinition(
                $expectedIndexName,
                ...$expectedForeignKey
            );

            $this->assertEquals(
                $expectedForeignKeyDefinition,
                $actualIndexes[$expectedIndexName],
                sprintf('Index structure is different for %s', $expectedIndexName)
            );
        }
    }

    /**
     * @param string $columnName
     * @param array $column
     * @return array
     */
    private function expectedColumnDefinition($columnName, $column): array
    {
        return [
            'COLUMN_NAME' => $columnName,
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
        ];
    }

    /**
     * @param string $indexName
     * @param string $indexType
     * @param string[] $indexColumns
     * @return array
     */
    private function expectedIndexDefinition($indexName, $indexType, array $indexColumns): array
    {
        $expectedColumns = [];
        foreach ($indexColumns as $position => $column) {
            $expectedColumns[strtoupper($column)] = [
                'NAME' => $column,
                'SIZE' => null,
                'POSITION' => $position
            ];
        }
        $expectedIndexDefinition = [
            'INDEX_NAME' => $indexName,
            'COLUMNS' => $expectedColumns,
            'TYPE' => $indexType
        ];

        return $expectedIndexDefinition;
    }

    /**
     * @param string $expectedIndexName
     * @param string $column
     * @param string $referenceTable
     * @param string $referenceColumn
     * @param string $onDelete
     * @return array
     */
    private function expectedForeignKeyDefinition(
        $expectedIndexName,
        $column,
        $referenceTable,
        $referenceColumn,
        $onDelete
    ): array {

        return [
            'FK_NAME' => $expectedIndexName,
            'COLUMN_NAME' => $column,
            'REF_TABLE_NAME' => $referenceTable,
            'REF_COLUMN_NAME' => $referenceColumn,
            'ON_DELETE' => $onDelete
        ];
    }

    private function configureTableDefinition(TableDefinition $definition, $tablePrefix = ''): Table
    {
        return $definition->configure(new Table(),  new FakeNameResolver($tablePrefix));
    }

    /**
     * @return TableDefinitionBuilder
     */
    private function createBuilder(): TableDefinitionBuilder
    {
        return TableDefinitionBuilder::create()->newTable();
    }
}
