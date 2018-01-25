<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use EcomDev\DatabaseWrapper\TestUtilities\ConfiguredObjectManagerFactory;
use EcomDev\DatabaseWrapper\TestUtilities\SimpleDatabaseTestCase;
use EcomDev\DatabaseWrapper\TestUtilities\TestConfigurationFactory;
use Magento\Framework\App\ResourceConnection;

/** @medium */
class MagentoNameResolverTest extends SimpleDatabaseTestCase
{
    /** @var ResourceConnection */
    private $resourceConnection;

    /** @var MagentoNameResolverFactory */
    private $resolverFactory;

    protected function setUp()
    {
        $this->resourceConnection = $this->createAdapter()->createResourceConnection('mgt_');
        $this->resolverFactory = new MagentoNameResolverFactory(
            ConfiguredObjectManagerFactory::createObjectManager(TestConfigurationFactory::createFromEnvironment())
        );
    }

    /**
     * @testWith
     *  ["table_name", "mgt_table_name"]
     *  [["table_name", "int"], "mgt_table_name_int"]
     */
    public function resolvesStringTableNameOfTableViaMagento($tableAlias, $tableName)
    {
        $this->assertEquals(
            $tableName,
            $this->createResolver()->resolveTableName($tableAlias)
        );
    }

    /** @test */
    public function resolvesShortIndexNameViaMagento()
    {
        $this->assertEquals(
            'MGT_TABLE_NAME_INDEXED_COLUMN',
            $this->createResolver()->resolveIndexName('table_name', ['indexed_column'], 'index')
        );
    }

    /** @test */
    public function resolvesLongIndexNameViaMagento()
    {
        $this->assertEquals(
            'UNQ_FF4AA8BE7D40EDFBB54D168C0CD16F49',
            $this->createResolver()->resolveIndexName(
                ['very_long_table_name', 'with_suffix'],
                ['column1', 'column2', 'column3', 'column4', 'column5'],
                'unique'
            )
        );
    }

    /** @test */
    public function resolvesShortForeignKeyNameViaMagento()
    {
        $this->assertEquals(
            'MGT_TABLE_NAME_SUFFIX_FOREIGN_ID_MGT_REFERENCE_TABLE_ID',
            $this->createResolver()->resolveForeignKeyName(
                ['table_name', 'suffix'],
                'foreign_id',
                'reference_table',
                'id'
            )
        );
    }

    /** @test */
    public function resolvesLongForeignKeyViaMagento()
    {
        $this->assertEquals(
            'FK_CBCDD8188F20E96D811B6F8ED07CBFB4',
            $this->createResolver()->resolveForeignKeyName(
                ['long_table_name', 'with_suffix'],
                'foreign_id',
                ['reference_table', 'with_suffix'],
                'id'
            )
        );
    }

    private function createResolver(): MagentoNameResolver
    {
        return $this->resolverFactory->createResolver(
            $this->resourceConnection
        );
    }
}
