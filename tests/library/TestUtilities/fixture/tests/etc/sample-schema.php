<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;

return [
    TableDefinitionBuilder::create()
        ->newTable()
        ->withName('table3')
        ->withIdentity('id')
        ->build(),
    TableDefinitionBuilder::create()
        ->newTable()
        ->withName('table4')
        ->withIdentity('id')
        ->build()
];
