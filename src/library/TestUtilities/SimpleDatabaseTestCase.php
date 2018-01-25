<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use PHPUnit\Framework\TestCase;

abstract class SimpleDatabaseTestCase extends TestCase
{
    /**
     * @var TestDatabaseAdapter
    */
    private static $adapter;

    protected static function createAdapter()
    {
        if (!self::$adapter) {
            $factory = new TestDatabaseFactory();
            self::$adapter = $factory->createTestDbAdapter();
        }

        return self::$adapter;
    }

    protected static function createConnection($tablePrefix = ''): DatabaseConnection
    {
        return self::createAdapter()->createConnection($tablePrefix);
    }

    public static function tearDownAfterClass()
    {
        self::$adapter = null;
    }
}
