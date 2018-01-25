<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

/**
 * Permanent table implementation
 */
class PermanentTable implements Table
{
    /**
     * Name of the table
     *
     * @var string
     */
    private $name;

    /**
     * Creates table instance
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns table name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
