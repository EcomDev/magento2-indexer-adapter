<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

class QueryResult implements \IteratorAggregate
{

    /**
     * @var \Zend_Db_Statement_Interface
     */
    private $statement;

    public function __construct(\Zend_Db_Statement_Interface $statement)
    {
        $this->statement = $statement;
    }

    public function getIterator()
    {
        return new \IteratorIterator($this->statement);
    }

    public function fetchOne($columnIndex = 0)
    {
        return $this->statement->fetchColumn($columnIndex);
    }

    public function fetchCol($columnIndex = 0)
    {
        return $this->statement->fetchAll(\Zend_Db::FETCH_COLUMN, $columnIndex);
    }

    public function fetchPairs()
    {
        $data = [];
        while (($row = $this->statement->fetch(\Zend_Db::FETCH_NUM))) {
            $data[$row[0]] = $row[1];
        }
        return $data;
    }

    public function fetchAssoc()
    {
        $data = [];
        while (($row = $this->statement->fetch(\Zend_Db::FETCH_ASSOC))) {
            $firstColumn = key($row);
            $data[$row[$firstColumn]] = $row;
        }

        return $data;
    }

    public function fetchAll()
    {
        return iterator_to_array($this);
    }
}
