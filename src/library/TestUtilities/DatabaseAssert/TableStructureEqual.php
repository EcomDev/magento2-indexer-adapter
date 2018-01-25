<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities\DatabaseAssert;

use EcomDev\DatabaseWrapper\Db\DatabaseConnection;
use EcomDev\DatabaseWrapper\Db\Table;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator;

use PHPUnit\Framework\Constraint\Constraint;

class TableStructureEqual extends Constraint
{
    /**
     * Factory for comparing two strings
     *
     * @var Comparator\Factory
     */
    private $comparatorFactory;

    /**
     * Expected structure of table
     *
     * @var string
     */
    private $expectedStructure;

    /**
     * Test database adapter
     *
     * @var DatabaseConnection
     */
    private $connection;

    /**
     * Configures assertion
     *
     * @param string $expectedStructure
     * @param DatabaseConnection $connection
     */
    public function __construct(string $expectedStructure, DatabaseConnection $connection)
    {
        $this->comparatorFactory = Comparator\Factory::getInstance();
        $this->expectedStructure = $expectedStructure;
        $this->connection = $connection;
    }


    /**
     * Evaluates constraint on a table object
     *
     * @param Table|string $other
     * @param string $description
     * @param bool $returnResult
     *
     * @return bool
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        $tableName = $other instanceof Table ? $other->getName() : $other;

        $expectedStructure = $this->normalizeStatement($this->expectedStructure);
        $actualStructure = $this->normalizeStatement(
            $this->connection->executeQuery(sprintf('SHOW CREATE TABLE %s', $tableName))->fetchOne(1)
        );


        try {
            $comparator = $this->comparatorFactory->getComparatorFor(
                $expectedStructure,
                $actualStructure
            );

            $comparator->assertEquals(
                $actualStructure,
                $expectedStructure,
                0.0,
                false,
                true
            );
        } catch (Comparator\ComparisonFailure $comparisonFailure) {
            if ($returnResult) {
                return false;
            }

            throw new ExpectationFailedException(
                \trim($description . "\n" . $comparisonFailure->getMessage()),
                $comparisonFailure
            );
        }

        return true;
    }

    /**
     * Normalize database statement to a single indented
     *
     * @param string $databaseStatement
     *
     * @return string
     */
    public function normalizeStatement(string $databaseStatement)
    {
        $lines = array_filter(explode(PHP_EOL, $databaseStatement), 'trim');

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
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return \sprintf(
            'is equal to: %s%s',
            PHP_EOL,
            $this->expectedStructure
        );
    }
}
