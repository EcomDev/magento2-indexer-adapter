<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;

use EcomDev\IndexerWrapper\Db\DbAdapter;
use EcomDev\IndexerWrapper\Db\Statement\Callback;
use PHPUnit\DbUnit\Database\DefaultConnection;

/**
 * Test connection for tests
 *
 *
 */
class Connection extends DefaultConnection
{
    public function __construct(DbAdapter $dbAdapter)
    {
        $pdoConnection = $dbAdapter->execute(new Callback(function ($adapter) {
            return $adapter->getConnection();
        }));

        parent::__construct($pdoConnection, $dbAdapter->getSchema());
    }

}
