<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use EcomDev\DatabaseWrapper\Db\DbAdapter;
use EcomDev\DatabaseWrapper\Db\MagentoDatabaseConnection;
use EcomDev\DatabaseWrapper\Db\PermanentTableFactory;
use EcomDev\DatabaseWrapper\Db\SimpleDbAdapter;
use EcomDev\DatabaseWrapper\Db\SimpleDbAdapterFactory;
use EcomDev\DatabaseWrapper\Db\Statement\Callback;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\MysqlFactory;

class TestDatabaseFactory
{
    /**
     * Test configuration factory
     *
     * @var FileBasedTestConfiguration
     */
    private $configuration;

    private $connection;

    public function __construct(FileBasedTestConfiguration $configuration = null)
    {
        $this->configuration = $configuration ?? TestConfigurationFactory::createFromEnvironment();
    }

    public function getPdoConnection(): \PDO
    {
        if (!$this->connection) {
            $this->connection = $this->createPdoConnection();
        }

        return $this->connection;
    }

    public function createTestDbAdapter(array $schema = null): TestDatabaseAdapter
    {
        $decoratedConfiguration = [
            'connection' => ['db_name_prefix' => 'db_unit_test'],
            'schema' => $schema
        ];

        $configuration = TestConfigurationFactory::createDecoratedConfiguration(
            $this->configuration,
            $decoratedConfiguration
        );

        $objectManager = ConfiguredObjectManagerFactory::createObjectManager($configuration);

        return $objectManager->create(TestDatabaseAdapter::class);
    }


    /**
     * @return \PDO
     */
    private function createPdoConnection(): \PDO
    {
        $connectionConfiguration = $this->configuration->getConnection();

        return new \PDO(
            sprintf('mysql:host=%s;charset=UTF8', $connectionConfiguration['host']),
            $connectionConfiguration['username'],
            $connectionConfiguration['password']
        );
    }
}
