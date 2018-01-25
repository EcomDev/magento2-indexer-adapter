<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use EcomDev\DatabaseWrapper\Db\TableDefinition;
use PHPUnit\DbUnit\TestCase;
use PHPUnit\Util\Test;

abstract class FixtureBasedDatabaseTestCase extends TestCase
{
    private $defaultDBConnection;
    /**
     * @var TestDatabaseAdapter
     */
    private static $adapter;

    public static function setUpBeforeClass()
    {
        $testDatabaseFactory = new TestDatabaseFactory();

        $className = \get_called_class();

        $schema = self::readSchemaFromClassAnnotation($className);

        self::$adapter = $testDatabaseFactory->createTestDbAdapter($schema);
        self::$adapter->loadSchemaFixture();
    }

    /**
     * @param string $className
     * @return TableDefinition[]|null
     */
    private static function readSchemaFromClassAnnotation($className)
    {
        $annotations = Test::parseTestMethodAnnotations($className);

        $reflectionClass = new \ReflectionClass($className);

        $basePath = dirname($reflectionClass->getFileName());

        $schema = null;

        if (isset($annotations['class']['schema'])) {
            $schemaFile = current($annotations['class']['schema']);
            $schema = include $basePath . DIRECTORY_SEPARATOR . $schemaFile;
        }

        return $schema;
    }

    protected function getConnection()
    {
        if ($this->defaultDBConnection === null) {
            $this->defaultDBConnection = self::$adapter->createDBUnitConnection();
        }

        return $this->defaultDBConnection;
    }

    protected function getAdapter(): TestDatabaseAdapter
    {
        return self::$adapter;
    }

    protected function createConnection($tablePrefix = ''): DatabaseConnection
    {
        return $this->getAdapter()->createConnection($tablePrefix);
    }

    public static function tearDownAfterClass()
    {
        self::$adapter = null;
    }
}
