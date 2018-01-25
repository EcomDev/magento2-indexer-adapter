<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use EcomDev\DatabaseWrapper\Db\MagentoNameResolver;
use EcomDev\DatabaseWrapper\Db\MagentoNameResolverFactory;
use EcomDev\DatabaseWrapper\Db\NameResolver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class TestDatabaseConnectionFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function createConnection(
        ResourceConnection $resourceConnection,
        string $resourceName = null
    ): DatabaseConnection {
        $arguments = [
            'resourceConnection' => $resourceConnection
        ];

        if ($resourceName) {
            $arguments['resourceName'] = $resourceName;
        }

        return $this->objectManager->create(DatabaseConnection::class, $arguments);
    }
}
