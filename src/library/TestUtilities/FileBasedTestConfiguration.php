<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\TableDefinition;

class FileBasedTestConfiguration implements TestConfiguration
{
    /**
     * Configuration array
     *
     * @var array
     */
    private $configuration;

    /**
     * Expects all path provided in
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * Returns schema name
     *
     * @return TableDefinition[]
     */
    public function getSchema(): array
    {
        if (empty($this->configuration['SCHEMA_FILE'])) {
            return [];
        }

        return $this->loadPhpFile('SCHEMA_FILE');
    }

    /**
     * Returns root of the project
     *
     * @return string
     */
    public function getProjectRoot()
    {
        if (!isset($this->configuration['PROJECT_ROOT'])) {
            $this->configuration['PROJECT_ROOT'] = $this->findPathToPHPUnitConfigurationFile();
        }

        return $this->configuration['PROJECT_ROOT'];
    }

    /**
     * Returns dependency injection configuration
     *
     * @return array
     */
    public function getDependencyInjection(): array
    {
        return $this->loadPhpFile('DI_FILE');
    }

    /**
     * Returns array of database connection settings
     *
     * @return array
     */
    public function getConnection(): array
    {
        return $this->loadPhpFile('CONNECTION_FILE');
    }

    private function findPathToPHPUnitConfigurationFile()
    {
        $currentDirectory = getcwd();

        $hasPhpUnitXml = function ($path) {
            return file_exists($path . DIRECTORY_SEPARATOR . 'phpunit.xml')
                || file_exists($path . DIRECTORY_SEPARATOR . 'phpunit.xml.dist');
        };

        // Traverse up to root in search of PHPUnit configuration file
        while ($currentDirectory !== DIRECTORY_SEPARATOR && !$hasPhpUnitXml($currentDirectory)) {
            $currentDirectory = dirname($currentDirectory);
        }

        return $currentDirectory;
    }

    private function resolveFilePath($filePath)
    {
        if (strpos($filePath, DIRECTORY_SEPARATOR) === 0) {
            return $filePath;
        }

        return $this->getProjectRoot() . DIRECTORY_SEPARATOR . $filePath;
    }

    /**
     * @param $fileKey
     * @return array
     */
    private function loadPhpFile($fileKey): array
    {
        $filePath = $this->resolveFilePath($this->configuration[$fileKey]);

        return include $filePath;
    }
}
