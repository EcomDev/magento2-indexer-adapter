<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

/**
 * Fake PDO statement for testing
 */
class TestPdoStatement extends \Zend_Test_DbStatement implements \IteratorAggregate
{
    public static function createFromArray(array $rows)
    {
        $statement = new self();

        foreach ($rows as $row) {
            $statement->append($row);
        }

        return $statement;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fetchAll());
    }

    public function fetchAll($style = null, $col = null)
    {
        $rows = parent::fetchAll($style, $col);
        $col = $col ?? 0;
        if ($style === \Zend_Db::FETCH_COLUMN) {
            return array_map(
                function ($row) use ($col) {
                    $columnNames = array_keys($row);
                    return $row[$columnNames[$col]];
                },
                $rows
            );
        }

        return $rows;
    }

    public function fetch($style = null, $cursor = null, $offset = null)
    {
        $row = parent::fetch($style, $cursor, $offset);

        if ($row && $style === \Zend_Db::FETCH_NUM) {
            return array_values($row);
        }

        return $row;
    }
}
