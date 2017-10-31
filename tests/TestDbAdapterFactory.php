<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;

use EcomDev\IndexerWrapper\Db\DbAdapter;
use EcomDev\IndexerWrapper\Db\SimpleDbAdapter;
use EcomDev\IndexerWrapper\Db\SimpleDbAdapterFactory;
use EcomDev\IndexerWrapper\Db\Statement\Callback;
use EcomDev\IndexerWrapper\Db\TestDbAdapter;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\MysqlFactory;

class TestDbAdapterFactory
{
    public static function createSimpleDbAdapter(array $configuration = []): SimpleDbAdapter
    {
        $environmentConfiguration = new EnvironmentConfiguration();

        $simpleDbAdapterFactory = new SimpleDbAdapterFactory(
            $configuration + $environmentConfiguration->export() + ['dbname' => 'information_schema'],
            (new ConfiguredObjectManagerFactory())->createObjectManager()->create(MysqlFactory::class)
        );

        return $simpleDbAdapterFactory->create();
    }

    public static function createTestDbAdapter(
        $prefix = 'db_unit_test',
        DbAdapter $dbAdapter = null
    ): TestDbAdapter {
        return new TestDbAdapter($dbAdapter ?: self::createSimpleDbAdapter(), $prefix);
    }

    public static function createQuery($query, $bind = [])
    {
        return new Callback(function (AdapterInterface $magentoAdapter) use ($query, $bind) {
            return $magentoAdapter->query($query, $bind);
        });
    }

    public static function createSingleRowQuery($query, $bind = [])
    {
        return new Callback(function (AdapterInterface $magentoAdapter) use ($query, $bind) {
            return $magentoAdapter->fetchRow($query, $bind);
        });
    }
}
