<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;
use EcomDev\DatabaseWrapper\Db\TableDefinition;

$tableBuilder = \EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder::create();

$productTable = $tableBuilder
    ->newTable()
    ->withName('product')
    ->withIdentity('product_id')
    ->withColumn('sku', TableDefinition::TYPE_TEXT, 255)
    ->withColumnWithoutSize('position', TableDefinition::TYPE_INTEGER)
    ->build()
;

$productPriceTable = $tableBuilder
    ->newTable()
    ->withName('product_price')
    ->withColumnWithoutSize('product_id', TableDefinition::TYPE_INTEGER, ['unsgined' => true])
    ->withColumn('sku', TableDefinition::TYPE_TEXT, 255)
    ->withColumn('price', TableDefinition::TYPE_DECIMAL, [12, 4], ['unsigned' => true])
    ->build()
;

return [
    $productTable,
    $productPriceTable,
    $tableBuilder
        ->newTableFrom($productTable)
        ->withName('prefixed_product')
        ->build(),
    $tableBuilder
        ->newTableFrom($productPriceTable)
        ->withName('prefixed_product_price')
        ->build(),


];
