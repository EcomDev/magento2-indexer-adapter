<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class DatabaseConnection
{
    /**
     * @var QueryResultFactory
     */
    private $queryResultFactory;
    /**
     * @var string
     */
    private $resourceName;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var MagentoNameResolverFactory
     */
    private $resolverFactory;

    public function __construct(
        ResourceConnection $resourceConnection,
        QueryResultFactory $queryResultFactory,
        MagentoNameResolverFactory $resolverFactory,
        string $resourceName = ResourceConnection::DEFAULT_CONNECTION
    ) {
        $this->queryResultFactory = $queryResultFactory;
        $this->resourceName = $resourceName;
        $this->resourceConnection = $resourceConnection;
        $this->resolverFactory = $resolverFactory;
    }

    public function createTable(TableDefinition $table): Table
    {
        $magentoTableObject = $table->configure(
            $this->getMagentoAdapter()->newTable(),
            $this->resolverFactory->createResolver($this->resourceConnection)
        );

        $this->getMagentoAdapter()->createTable($magentoTableObject);

        return new PermanentTable($magentoTableObject->getName());
    }

    public function createTableFromQuery(Query $select, TableDefinition $table)
    {
        // TODO: Implement createTableFromSelect() method.
    }

    public function transactional(callable $operation)
    {
        // TODO: Implement transactional() method.
    }

    public function executeQuery($query, array $bind = []): QueryResult
    {
        return $this->queryResultFactory->createFromPdoStatement(
            $this->getMagentoAdapter()->query($query, $bind)
        );
    }

    public function executeCommand($command, array $bind = [])
    {
        // TODO: Implement executeCommand() method.
    }

    private function getMagentoAdapter(): Mysql
    {
        return $this->resourceConnection->getConnection($this->resourceName);
    }
}
