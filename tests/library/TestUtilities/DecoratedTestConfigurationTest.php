<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;


use EcomDev\DatabaseWrapper\Db\TableDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class DecoratedTestConfigurationTest extends TestCase
{
    /** @test */
    public function keepsSchemaAsInOriginalConfigurationWhenNoSchemaProvided()
    {
        $configuration = $this->createDecoratedConfiguration([]);
        $this->assertEquals(
            [
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table3')
                    ->withIdentity('id')
                    ->build(),
                TableDefinitionBuilder::create()
                    ->newTable()
                    ->withName('table4')
                    ->withIdentity('id')
                    ->build(),
            ],
            $configuration->getSchema()
        );
    }

    /** @test */
    public function replacesSchemaWhenDecoratedValueForItIsProvided()
    {
        $configuration = $this->createDecoratedConfiguration(['schema' => []]);

        $this->assertEquals([], $configuration->getSchema());
    }

    /** @test */
    public function mergesValuesToExistingConnectionSettings()
    {
        $configuration = $this->createDecoratedConfiguration(['connection' => ['some_value' => 'some_value']]);

        $this->assertEquals(
            [
                'some_data' => 'some_data',
                'some_value' => 'some_value'
            ],
            $configuration->getConnection()
        );
    }

    private function createRealFileBasedConfiguration(): TestConfiguration
    {
        return TestConfigurationFactory::createFileConfiguration(
            'sample-file.php',
            'sample-file.php',
            'sample-schema.php',
            __DIR__ . '/fixture/tests/etc/'
        );
    }

    private function createDecoratedConfiguration(array $decoratedData): DecoratedTestConfiguration
    {
        return TestConfigurationFactory::createDecoratedConfiguration(
            $this->createRealFileBasedConfiguration(),
            $decoratedData
        );
    }
}
