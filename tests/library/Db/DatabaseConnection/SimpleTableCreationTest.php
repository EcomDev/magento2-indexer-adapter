<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db\DatabaseConnection;

use EcomDev\DatabaseWrapper\Db\TableDefinition;
use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;
use EcomDev\DatabaseWrapper\TestUtilities\SimpleDatabaseTestCase;
use EcomDev\DatabaseWrapper\TestUtilities\DatabaseAssert;

/** @large */
class SimpleTableCreationTest extends SimpleDatabaseTestCase
{
    /** @var DatabaseAssert */
    private $databaseAssert;

    protected function setUp()
    {
        $this->databaseAssert = new DatabaseAssert($this->createAdapter());
    }

    /** @test */
    public function createsSimpleTableFromTableDefinition()
    {
        $factory = $this->createConnection();

        $this->databaseAssert->assertTableStructure(
            "
            CREATE TABLE `simple_table_name` (
                `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity_id',
                `value` varchar(255) NOT NULL COMMENT 'Value',
                PRIMARY KEY (`entity_id`), 
                KEY `SIMPLE_TABLE_NAME_VALUE` (`value`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='simple_table_name'
            ",
            $factory->createTable(
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('simple_table_name')
                    ->withIdentity('entity_id')
                    ->withColumn('value', TableDefinition::TYPE_TEXT, 255)
                    ->withIndex('value')
                    ->build()
            )
        );
    }

    /** @test */
    public function createsSimpleTableFromTableDefinitionWithPrefixedName()
    {
        $factory = $this->createConnection('prefix_');

        $this->databaseAssert->assertTableStructure(
            "
            CREATE TABLE `prefix_simple_table_name` (
                `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity_id',
                `value` varchar(255) NOT NULL COMMENT 'Value',
                PRIMARY KEY (`entity_id`), 
                KEY `PREFIX_SIMPLE_TABLE_NAME_VALUE` (`value`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='prefix_simple_table_name'
            ",
            $factory->createTable(
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('simple_table_name')
                    ->withIdentity('entity_id')
                    ->withColumn('value', TableDefinition::TYPE_TEXT, 255)
                    ->withIndex('value')
                    ->build()
            )
        );
    }

    /** @test */
    public function createsTableWithForeignKeyFromTableDefinition()
    {
        $factory = $this->createConnection('prefix_');

        $factory->createTable(
            TableDefinitionBuilder::create()
                ->newTable()
                ->withName('src_table')
                ->withIdentity('id')
                ->build()
        );

        $this->databaseAssert->assertTableStructure(
            "
            CREATE TABLE `prefix_table_with_foreign_key` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
                  `reference_id` int(10) unsigned NOT NULL COMMENT 'Reference_id',
                  PRIMARY KEY (`id`),
                  KEY `PREFIX_TABLE_WITH_FOREIGN_KEY_REFERENCE_ID_PREFIX_SRC_TABLE_ID` (`reference_id`),
                  CONSTRAINT `PREFIX_TABLE_WITH_FOREIGN_KEY_REFERENCE_ID_PREFIX_SRC_TABLE_ID` FOREIGN KEY (`reference_id`) REFERENCES `prefix_src_table` (`id`) ON DELETE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='prefix_table_with_foreign_key'
            ",
            $factory->createTable(
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table_with_foreign_key')
                    ->withIdentity('id')
                    ->withColumnWithoutSize('reference_id', TableDefinition::TYPE_INTEGER, ['unsigned' => true])
                    ->withForeignKey('reference_id', 'src_table', 'id')
                    ->build()
            )
        );
    }

    /** @test */
    public function createsTableWithCustomOptionsFromTableDefinition()
    {
        $factory = $this->createConnection();

        $this->databaseAssert->assertTableStructure(
            "
            CREATE TABLE `table_with_custom_options` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
                  PRIMARY KEY (`id`)
            ) ENGINE=MEMORY AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='table_with_custom_options'
            ",
            $factory->createTable(
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table_with_custom_options')
                    ->withIdentity('id')
                    ->withOption('type', 'MEMORY')
                    ->withOption('auto_increment', 1000)
                    ->build()
            )
        );
    }
}
