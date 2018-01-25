<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

interface QueryExpression
{
    /**
     * Must return an executable data query as an array pair of query and binding
     *
     * @param  DatabaseConnection $databaseConnection
     * @return array
     */
    public function export(DatabaseConnection $databaseConnection);
}
