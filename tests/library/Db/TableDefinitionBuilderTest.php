<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use PHPUnit\Framework\TestCase;

/**
 * Use cases for table definition builder
 */
class TableDefinitionBuilderTest extends TestCase
{
    /**
     * Expected unsigned integer column definition
     */
    const INTEGER_COLUMN_DEFINITION_UNSIGNED_AND_NOT_NULL = [
        'type' => TableDefinition::TYPE_INTEGER,
        'size' => null,
        'options' => [
            'unsigned' => true,
            'nullable' => false
        ]
    ];

    /**
     * Expected text column definition
     */
    const TEXT_COLUMN_DEFINITION_255_NOT_NULL = [
        'type' => TableDefinition::TYPE_TEXT,
        'size' => 255,
        'options' => [
            'nullable' => false
        ]
    ];

    /**
     * Expected nullable text column definition
     */
    const TEXT_COLUMN_DEFINITION_255_NULLABLE = [
        'type' => TableDefinition::TYPE_TEXT,
        'size' => 255,
        'options' => [
            'nullable' => true
        ]
    ];

    /**
     * Identity column definition
     */
    const IDENTITY_COLUMN_DEFINITION = [
        'type' => TableDefinition::TYPE_INTEGER,
        'size' => null,
        'options' => [
            'nullable' => false,
            'unsigned' => true,
            'identity' => true,
            'primary' => true
        ]
    ];

    /** @test */
    public function createsTableDefinitionWithTableName()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('my_table_name')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition('my_table_name'),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithNotNullableColumnWhenNoOptionsProvided()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['text_column' => self::TEXT_COLUMN_DEFINITION_255_NOT_NULL]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithNotNullableColumnWhenOptionsDoNotOverrideValue()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withColumn('int_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['int_column' => self::INTEGER_COLUMN_DEFINITION_UNSIGNED_AND_NOT_NULL]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithNullableColumnWhenOptionsAreProvided()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255, ['nullable' => true])
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['text_column' => self::TEXT_COLUMN_DEFINITION_255_NULLABLE]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithIdentifierColumn()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['primary_column' => self::IDENTITY_COLUMN_DEFINITION]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithMultipleIndexesOnExistingColumns()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('id')
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withIndex('id', 'text_column')
            ->withIndex('text_column')
            ->build()
        ;


        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                [
                    'id' => self::IDENTITY_COLUMN_DEFINITION,
                    'text_column' => self::TEXT_COLUMN_DEFINITION_255_NOT_NULL
                ],
                [
                    [['id', 'text_column'], 'index'],
                    [['text_column'], 'index']
                ]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionUniqueIndexOnExistingColumns()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('id')
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withUnique('id', 'text_column')
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                [
                    'id' => self::IDENTITY_COLUMN_DEFINITION,
                    'text_column' => self::TEXT_COLUMN_DEFINITION_255_NOT_NULL
                ],
                [
                    [['id', 'text_column'], 'unique']
                ]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithFulltextIndex()
    {
        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('id')
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withFulltext('text_column')
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                [
                    'id' => self::IDENTITY_COLUMN_DEFINITION,
                    'text_column' => self::TEXT_COLUMN_DEFINITION_255_NOT_NULL
                ],
                [
                    [['text_column'], 'fulltext']
                ]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithForeignKeyWithoutAction()
    {

        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withColumn('foreign_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->withForeignKey('foreign_column', 'other_table', 'other_primary_column')
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['foreign_column' => self::INTEGER_COLUMN_DEFINITION_UNSIGNED_AND_NOT_NULL],
                [],
                [
                    ['foreign_column', 'other_table', 'other_primary_column', null]
                ]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionWithForeignKeyWithCascadeAction()
    {

        $tableDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withColumn('foreign_column', TableDefinition::TYPE_INTEGER, null, ['unsigned' => true])
            ->withForeignKey('foreign_column', 'other_table', 'other_primary_column', 'CASCADE')
            ->build()
        ;

        $this->assertTableDefinitionsAreSimilar(
            new TableDefinition(
                'example_table',
                ['foreign_column' => self::INTEGER_COLUMN_DEFINITION_UNSIGNED_AND_NOT_NULL],
                [],
                [
                    ['foreign_column', 'other_table', 'other_primary_column', 'CASCADE']
                ]
            ),
            $tableDefinition
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingEmptyTableDefinition()
    {
        $existingDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $existingDefinition,
            $this->createTableBuilder()->newTableFrom($existingDefinition)->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingTableDefinitionWithColumns()
    {
        $existingDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $existingDefinition,
            $this->createTableBuilder()->newTableFrom($existingDefinition)->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingTableDefinitionWithIndexes()
    {
        $existingDefinition = $this->createTableBuilder()->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withIndex('int_column')
            ->withUnique('int_column', 'text_column')
            ->withFulltext('text_column')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $existingDefinition,
            $this->createTableBuilder()->newTableFrom($existingDefinition)->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingTableDefinitionWithForeignKeys()
    {
        $tableBuilder = $this->createTableBuilder();
        
        $existingDefinition = $tableBuilder->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('foreign_column', TableDefinition::TYPE_INTEGER, ['unsigned' => true])
            ->withForeignKey('foreign_column', 'other_table', 'other_primary_column', 'CASCADE')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $existingDefinition,
            $tableBuilder->newTableFrom($existingDefinition)->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingTableDefinitionWithOptions()
    {
        $tableBuilder = $this->createTableBuilder();

        $existingDefinition = $tableBuilder->newTable()
            ->withName('example_table')
            ->withIdentity('id')
            ->withOption('type', 'ENGINE')
            ->withOption('auto_increment', 999)
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $existingDefinition,
            $tableBuilder->newTableFrom($existingDefinition)->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingWithoutColumn()
    {
        $tableBuilder = $this->createTableBuilder();

        $existingDefinition = $tableBuilder->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $tableBuilder->newTable()
                ->withName('example_table')
                ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
                ->build(),
            $tableBuilder->newTableFrom($existingDefinition)
                ->withoutColumn('primary_column')
                ->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingWithoutForeignKeys()
    {
        $tableBuilder = $this->createTableBuilder();

        $existingDefinition = $tableBuilder->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withForeignKey('int_column', 'other_table', 'id')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $tableBuilder->newTable()
                ->withName('example_table')
                ->withIdentity('primary_column')
                ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
                ->build(),
            $tableBuilder->newTableFrom($existingDefinition)
                ->withoutForeignKeys()
                ->build()
        );
    }

    /** @test */
    public function createsTableDefinitionFromExistingWithoutKeys()
    {
        $tableBuilder = $this->createTableBuilder();

        $existingDefinition = $tableBuilder->newTable()
            ->withName('example_table')
            ->withIdentity('primary_column')
            ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
            ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
            ->withIndex('int_column')
            ->withUnique('primary_column', 'text_column')
            ->withFulltext('text_column')
            ->build();

        $this->assertTableDefinitionsAreSimilar(
            $tableBuilder->newTable()
                ->withName('example_table')
                ->withIdentity('primary_column')
                ->withColumnWithoutSize('int_column', TableDefinition::TYPE_INTEGER)
                ->withColumn('text_column', TableDefinition::TYPE_TEXT, 255)
                ->build(),
            $tableBuilder->newTableFrom($existingDefinition)
                ->withoutKeys()
                ->build()
        );
    }

    /**
     * @param TableDefinition $expectedDefinition
     * @param TableDefinition $actualDefinition
     */
    private function assertTableDefinitionsAreSimilar($expectedDefinition, $actualDefinition)
    {
        $this->assertNotSame(
            $expectedDefinition,
            $actualDefinition,
            'Should be different instances'
        );

        $this->assertEquals(
            $expectedDefinition,
            $actualDefinition,
            'Table definition structure must be the same'
        );
    }

    /**
     * @return TableDefinitionBuilder
     */
    private function createTableBuilder(): TableDefinitionBuilder
    {
        return TableDefinitionBuilder::create();
    }
}
