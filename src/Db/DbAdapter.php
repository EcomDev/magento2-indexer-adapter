<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;


interface DbAdapter
{
    /**
     * Resolves table name via adapter
     *
     * @param string $name
     * @return string
     */
    public function resolveTableName($name);

    /**
     * Executes query via internal adapter
     *
     * @param Statement $query
     * @return mixed
     */
    public function execute(Statement $query);

    /**
     * Returns currently selected database
     *
     * @return string
     */
    public function getSchema(): string;
}
