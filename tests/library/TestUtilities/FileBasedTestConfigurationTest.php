<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class FileBasedTestConfigurationTest extends TestCase
{
    /**
     * @var string
     */
    private $originalDirectory;

    /** @test */
    public function returnsProjectRootFromConfiguration()
    {
        $testConfiguration = $this->createConfiguration(['PROJECT_ROOT' => '/some/project/root']);

        $this->assertEquals(
            '/some/project/root',
            $testConfiguration->getProjectRoot()
        );
    }

    /**
     * @testWith
     *     ["very", "very"]
     *     ["very/deep/path", "very"]
     *     ["very/deep/with_own_config/another/path", "very/deep/with_own_config"]
     */
    public function detectsProjectRootFromLocationOfPhpunitConfigurationFile($testDirectory, $expectedRoot)
    {
        $prefix = __DIR__ . '/fixture/';
        chdir($prefix . $testDirectory);
        $testConfiguration = $this->createConfiguration([]);

        $this->assertEquals(
            $prefix . $expectedRoot,
            $testConfiguration->getProjectRoot()
        );
    }

    /** @test */
    public function readDiConfigurationFromFileWithAbsolutePath()
    {
        $testConfiguration = $this->createConfiguration(
            ['DI_FILE' => __DIR__ . '/fixture/tests/etc/sample-file.php']
        );

        $this->assertEquals(
            ['some_data' => 'some_data'],
            $testConfiguration->getDependencyInjection()
        );
    }

    /** @test */
    public function readDiConfigurationFromFileInProjectRoot()
    {
        $testConfiguration = $this->createConfiguration(
            [
                'PROJECT_ROOT' => __DIR__ . '/fixture/tests',
                'DI_FILE' => 'etc/sample-file.php'
            ]
        );

        $this->assertEquals(
            ['some_data' => 'some_data'],
            $testConfiguration->getDependencyInjection()
        );
    }

    /** @test */
    public function readConnectionConfigurationFromFileWithWhenAbsolutePathIsProvided()
    {
        $testConfiguration = $this->createConfiguration(
            ['CONNECTION_FILE' => __DIR__ . '/fixture/tests/etc/sample-connection.php']
        );

        $this->assertEquals(
            [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'initStatements' => 'SET NAMES utf8',
                'connections' => ['default', 'other_connection_name']
            ],
            $testConfiguration->getConnection()
        );
    }

    /** @test */
    public function readConnectionFromFileInProjectRoot()
    {
        $testConfiguration = $this->createConfiguration(
            [
                'PROJECT_ROOT' => __DIR__ . '/fixture/tests',
                'CONNECTION_FILE' => 'etc/sample-connection.php'
            ]
        );

        $this->assertEquals(
            [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'initStatements' => 'SET NAMES utf8',
                'connections' => ['default', 'other_connection_name']
            ],
            $testConfiguration->getConnection()
        );
    }

    /** @test */
    public function readSchemaFromFileWithAbsolutePath()
    {
        $testConfiguration = $this->createDefaultConfiguration();

        $this->assertEquals(
            [
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table3')
                    ->withIdentity('id')
                    ->build(),
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table4')
                    ->withIdentity('id')
                    ->build()
            ],
            $testConfiguration->getSchema()
        );
    }

    /** @test */
    public function readSchemaFromFileInProjectRoot()
    {
        $testConfiguration = $this->createConfiguration(
            [
                'PROJECT_ROOT' => __DIR__ . '/fixture/tests',
                'SCHEMA_FILE' => __DIR__ . '/fixture/tests/etc/sample-schema.php'
            ]
        );

        $this->assertEquals(
            [
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table3')
                    ->withIdentity('id')
                    ->build(),
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table4')
                    ->withIdentity('id')
                    ->build()
            ],
            $testConfiguration->getSchema()
        );
    }

    /** @test */
    public function returnsEmptySchemaIfNoFileProvided()
    {
        $testConfiguration = $this->createConfiguration(['SCHEMA_FILE' => '']);

        $this->assertEquals([], $testConfiguration->getSchema());
    }

    public function createConfiguration($config): FileBasedTestConfiguration
    {
        return new FileBasedTestConfiguration($config);
    }

    protected function setUp()
    {
        $this->originalDirectory = getcwd();
    }

    protected function tearDown()
    {
        chdir($this->originalDirectory);
    }

    /**
     * @return FileBasedTestConfiguration
     */
    private function createDefaultConfiguration(): FileBasedTestConfiguration
    {
        return TestConfigurationFactory::createFileConfiguration(
            'sample-data.php',
            'sample-connection.php',
            'sample-schema.php',
            __DIR__ . '/fixture/tests/etc/'
        );
    }
}
