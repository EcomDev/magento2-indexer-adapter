<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;

use EcomDev\IndexerWrapper\Db\Statement;
use EcomDev\IndexerWrapper\Db\TestDbAdapter;
use PHPUnit\Framework\TestCase;

abstract class LightDbTestCase extends TestCase
{
    /** @var TestDbAdapter */
    private static $adapter;

    public static function setUpBeforeClass()
    {
        self::$adapter = self::createAdapter();
    }

    protected function execute(Statement $statement, array $configuration = [])
    {
        if ($configuration) {
            return self::createAdapter($configuration)->execute($statement);
        }

        return self::$adapter->execute($statement);
    }

    private static function createAdapter(array $configuration = [])
    {
        return TestDbAdapterFactory::createTestDbAdapter(
            'db_light_test',
            TestDbAdapterFactory::createSimpleDbAdapter($configuration)
        );
    }

    public static function tearDownAfterClass()
    {
        self::$adapter = null;
    }
}
