<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\DbAdapter;
use EcomDev\DatabaseWrapper\Db\Statement\Callback;
use PHPUnit\DbUnit\Database\DefaultConnection;

/**
 * Test connection for tests
 */
class DbUnitConnection extends DefaultConnection
{
    public function __construct(DbAdapter $dbAdapter)
    {
        $pdoConnection = $dbAdapter->execute(new Callback(function ($adapter) {
            return $adapter->getConnection();
        }));

        parent::__construct($pdoConnection, $dbAdapter->getSchema());
    }
}
