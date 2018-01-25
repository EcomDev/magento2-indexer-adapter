<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

final class TableDefinitionBuilder
{
    /**
     * @var string
     */
    private $name;

    /**
     * Columns to be added to table
     *
     * @var array[]
     */
    private $columns = [];

    /**
     * @var string[]
     */
    private $keys = [];

    /**
     * @var string[]
     */
    private $foreignKeys = [];

    /**
     * Table options (engine, charset, auto-increment)
     *
     * @var array
     */
    private $options = [];

    public static function create(): TableDefinitionBuilder
    {
        return new self();
    }

    public function newTable(): TableDefinitionBuilder
    {
        return new self();
    }

    public function newTableFrom(TableDefinition $existingTableDefinition): TableDefinitionBuilder
    {
        return $existingTableDefinition->copy($this->newTable());
    }

    public function withName($name): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        $builder->name = $name;
        return $builder;
    }

    public function withColumn($name, $type, $size, array $options = []): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        $builder->columns[$name] = [
            'type' => $type,
            'size' => $size,
            'options' =>  $options + ['nullable' => false]
        ];
        return $builder;
    }

    public function withColumnWithoutSize($name, $type, array $options = [])
    {
        return $this->withColumn($name, $type, null, $options);
    }

    public function withIdentity($name): TableDefinitionBuilder
    {
        return $this->withColumnWithoutSize(
            $name,
            TableDefinition::TYPE_INTEGER,
            [
                'unsigned' => true,
                'primary' => true,
                'identity' => true
            ]
        );
    }

    public function withIndex(...$columnNames): TableDefinitionBuilder
    {
        return $this->withKey(TableDefinition::INDEX_TYPE_INDEX, $columnNames);
    }

    public function withUnique(...$columnNames): TableDefinitionBuilder
    {
        return $this->withKey(TableDefinition::INDEX_TYPE_UNIQUE, $columnNames);
    }


    public function withFulltext(...$columnNames): TableDefinitionBuilder
    {
        return $this->withKey(TableDefinition::INDEX_TYPE_FULLTEXT, $columnNames);
    }

    public function withKey($type, array $columnNames): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        $builder->keys[] = [$columnNames, $type];
        return $builder;
    }


    public function withForeignKey($column, $referenceTable, $referenceColumn, $action = null): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        $builder->foreignKeys[] = [$column, $referenceTable, $referenceColumn, $action];
        return $builder;
    }

    public function withoutColumn($string): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        unset($builder->columns[$string]);
        return $builder;
    }

    public function withOption($name, $value)
    {
        $builder = $this->duplicate();
        $builder->options[$name] = $value;
        return $builder;
    }

    public function build(): TableDefinition
    {
        return new TableDefinition(
            $this->name,
            $this->columns,
            $this->keys,
            $this->foreignKeys,
            $this->options
        );
    }

    private function duplicate(): TableDefinitionBuilder
    {
        return clone $this;
    }

    public function withoutForeignKeys(): TableDefinitionBuilder
    {
        $builder = $this->duplicate();
        $builder->foreignKeys = [];
        return $builder;
    }

    public function withoutKeys()
    {
        $builder = $this->duplicate();
        $builder->keys = [];
        return $builder;
    }
}
