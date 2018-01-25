<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;

class TestDeploymentConfigReader extends DeploymentConfig\Reader
{
    /**
     * @var TestConfiguration
     */
    private $configuration;

    /**
     * No dependencies of original class are used in test environment
     *
     * Uses test configurations to create new connection settings
     *
     * @param array $configuration
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Reads configuration for database connections only from configuration
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($fileKey = null)
    {
        $connections = [];

        $connectionNames = $connectionSettings['connections'] ?? [ResourceConnection::DEFAULT_CONNECTION];

        foreach ($connectionNames as $connectionName) {
            $connections[$connectionName] = array_diff_key($this->configuration, ['connections' => true]);
        }

        return [
            'db' => ['connection' => $connections]
        ];
    }
}
