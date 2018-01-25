<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

class Query
{
    /**
     * Parts of the select
     *
     * @var array
     */
    private $parts;

    public function __construct(array $parts = [])
    {
        $this->parts = $parts;
    }
}
