<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;


class TableDefinitionBuilder
{
    private $name;

    /**
     * Columns to be added to table
     *
     * @var array[]
     */
    private $columns = [];

    public function newTable()
    {
        return new self();
    }

    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function withColumn($name, $type, $size = null, array $options = [])
    {
        $this->columns[] = [
            'name' => $name,
            'type' => $type,
            'size' => $size,
            'options' => ['nullable' => false] + $options
        ];
        return $this;
    }

    public function build()
    {
        return new TableDefinition($this->name, $this->columns);
    }
}
