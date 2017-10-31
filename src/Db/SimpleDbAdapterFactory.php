<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Adapter\Pdo\MysqlFactory;

class SimpleDbAdapterFactory
{
    /**
     * Configuration of connection
     *
     * @var string[]
     */
    private $configuration;

    /**
     * MySQL connection factory
     *
     * @var MysqlFactory
     */
    private $mysqlFactory;

    public function __construct(array $configuration, MysqlFactory $mysqlFactory)
    {
        $this->configuration = $configuration;
        $this->mysqlFactory = $mysqlFactory;
    }

    /**
     * Creates database adapter instance
     *
     * @return SimpleDbAdapter
     */
    public function create(): SimpleDbAdapter
    {
        return new SimpleDbAdapter(
            $this->mysqlFactory->create(
                Mysql::class,
                $this->configuration
            ),
            $this->configuration['table_prefix'] ?? ''
        );
    }
}
