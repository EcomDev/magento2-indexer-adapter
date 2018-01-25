<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\TableDefinition;

/**
 * Decorates existing instance
 */
class DecoratedTestConfiguration implements TestConfiguration
{
    /**
     * @var TestConfiguration
     */
    private $originalConfiguration;
    /**
     * @var array
     */
    private $decoratedValues;

    /**
     * Uses decorated values for decorating appropriate method calls
     *
     * @param TestConfiguration $originalConfiguration
     * @param array $decoratedValues
     */
    public function __construct(TestConfiguration $originalConfiguration, array $decoratedValues)
    {
        $this->originalConfiguration = $originalConfiguration;
        $this->decoratedValues = $decoratedValues;
    }

    /**
     *
     *
     * @return TableDefinition[]
     */
    public function getSchema(): array
    {
        return $this->decoratedValues['schema'] ?? $this->originalConfiguration->getSchema();
    }

    public function getDependencyInjection(): array
    {
        return $this->originalConfiguration->getDependencyInjection();
    }

    public function getConnection(): array
    {
        return ($this->decoratedValues['connection'] ?? []) + $this->originalConfiguration->getConnection();
    }
}
