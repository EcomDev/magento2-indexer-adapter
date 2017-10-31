<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;


class EnvironmentConfiguration
{
    public function export(): array
    {
        return [
            'host' => $_ENV['DB_HOST'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'initStatements' => 'SET NAMES utf8'
        ];
    }

}
