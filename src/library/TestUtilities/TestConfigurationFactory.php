<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

class TestConfigurationFactory
{
    public static function createFromEnvironment(): FileBasedTestConfiguration
    {
        return new FileBasedTestConfiguration($_ENV);
    }

    public static function createFileConfiguration(
        string $diFile,
        string $connectionFile,
        string $schemaFile,
        string $rootDirectory = null
    ): FileBasedTestConfiguration {
        return new FileBasedTestConfiguration([
            'PROJECT_ROOT' => $rootDirectory,
            'DI_FILE' => $diFile,
            'CONNECTION_FILE' => $connectionFile,
            'SCHEMA_FILE' => $schemaFile
        ]);
    }

    public static function createDecoratedConfiguration(TestConfiguration $configuration, array $decoratedData)
    {
        return new DecoratedTestConfiguration($configuration, $decoratedData);
    }
}
