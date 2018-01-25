<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class TestResourceConnectionFactory
{
    /**
 * @var ObjectManager
*/
    private $objectManager;
    /**
     * @var TestConfiguration
     */
    private $configuration;

    public function __construct(TestConfiguration $configuration, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->configuration = $configuration;
    }

    public function createResourceConnection($databaseName, $tablePrefix = ''): ResourceConnection
    {
        return $this->objectManager->create(
            ResourceConnection::class,
            [
                'resourceConfig' => $this->createResourceConfigStub(),
                'deploymentConfig' => $this->objectManager->create(
                    DeploymentConfig::class,
                    ['reader' => new TestDeploymentConfigReader(
                        $this->configuration->getConnection() + ['dbname' => $databaseName]
                    )]
                ),
                'tablePrefix' => $tablePrefix
            ]
        );
    }

    private function createResourceConfigStub(): ResourceConnection\ConfigInterface
    {
        $resourceConfig = new class implements ResourceConnection\ConfigInterface
        {
            public function getConnectionName($resourceName)
            {
                return $resourceName;
            }
        };

        return $resourceConfig;
    }
}
