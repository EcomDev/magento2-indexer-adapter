<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use EcomDev\DatabaseWrapper\Db\Table;
use EcomDev\DatabaseWrapper\TestUtilities\DatabaseAssert\TableStructureEqual;
use PHPUnit\Framework\Assert;

class DatabaseAssert
{
    /**
     * Database adapter
     *
     * @var TestDatabaseAdapter
     */
    private $adapter;

    public function __construct(TestDatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function isTableStructureEqual(string $expectedTableStructure): TableStructureEqual
    {
        return new TableStructureEqual($expectedTableStructure, $this->adapter->createConnection());
    }

    public static function normalizeStatement($string)
    {
        $lines = array_filter(explode(PHP_EOL, $string), 'trim');

        $baseIndentedLength = array_reduce($lines, function ($baseIndentedLength, $line) {
            $currentIndentation = strlen($line) - strlen(ltrim($line));

            return min(
                $baseIndentedLength ?? $currentIndentation,
                $currentIndentation
            );
        });

        $indentString = array_reduce($lines, function ($indentString, $line) use ($baseIndentedLength) {
            $currentIndentString = substr($line, 0, strlen($line) - strlen(ltrim($line)));

            $currentIndentLength = strlen($currentIndentString) - $baseIndentedLength;

            if ($currentIndentLength <= 0 || ($indentString && strlen($currentIndentString) < $currentIndentLength)) {
                return $indentString;
            }

            return substr($currentIndentString, $baseIndentedLength);
        });

        $lines = array_map(function ($line) use ($baseIndentedLength, $indentString) {
            $line = substr($line, $baseIndentedLength);
            $line = str_replace($indentString, "\t", $line);
            return rtrim($line);
        }, $lines);

        return implode(PHP_EOL, $lines);
    }

    /**
     * Asserts table structure and ignores white-spaces in response from database
     *
     * @param string $expectedTableStructure
     * @param Table|string $actualTable
     *
     * @return void
     */
    public function assertTableStructure($expectedTableStructure, $actualTable)
    {
        Assert::assertThat(
            $actualTable,
            $this->isTableStructureEqual($expectedTableStructure)
        );
    }
}
