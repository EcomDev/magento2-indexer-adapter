<?php

use EcomDev\DatabaseWrapper\Db\TableDefinition;
use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;

return [
    TableDefinitionBuilder::create()
        ->newTable()
        ->withName('index_metadata')
        ->withColumn('index_code', TableDefinition::TYPE_TEXT, 255)
        ->withColumn('table_name', TableDefinition::TYPE_TEXT, 255)
        ->withUnique('index_code')
        ->build(),
    TableDefinitionBuilder::create()
        ->newTable()
        ->withName('some_entity')
        ->withIdentity('entity_id')
        ->withColumn('name', TableDefinition::TYPE_TEXT, 255)
        ->build(),
    TableDefinitionBuilder::create()
        ->newTable()
        ->withName('some_complex_entity')
        ->withIdentity('entity_id')
        ->withColumnWithoutSize('type', TableDefinition::TYPE_INTEGER)
        ->withColumn('price', TableDefinition::TYPE_DECIMAL, [12, 4])
        ->withColumnWithoutSize('customer_id', TableDefinition::TYPE_INTEGER)
        ->withColumnWithoutSize('purchase_date', TableDefinition::TYPE_DATETIME)
        ->build()
];
