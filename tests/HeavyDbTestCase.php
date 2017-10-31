<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;

use EcomDev\IndexerWrapper\Db\TestDbAdapter;
use PHPUnit\DbUnit\TestCase;

abstract class HeavyDbTestCase extends TestCase
{
    /** @var TestDbAdapter */
    private static $adapter;

    public static function setUpBeforeClass()
    {
        self::$adapter = TestDbAdapterFactory::createTestDbAdapter();
        self::$adapter->loadFixture([]);
    }

    protected function getConnection()
    {
        return new Connection(self::$adapter);
    }

    public static function tearDownAfterClass()
    {
        self::$adapter = null;
    }
}
