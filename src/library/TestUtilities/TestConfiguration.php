<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\TableDefinition;

interface TestConfiguration
{
    /**
     * Returns schema name
     *
     * @return TableDefinition[]
     */
    public function getSchema(): array;

    /**
     * Returns dependency injection configuration
     *
     * @return array
     */
    public function getDependencyInjection(): array;

    /**
     * Returns array of database connection settings
     *
     * @return array
     */
    public function getConnection(): array;
}
