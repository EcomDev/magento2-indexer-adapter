<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db;

use Magento\Framework\DB\Adapter\AdapterInterface;

interface Statement
{
    public function execute(AdapterInterface $magentoAdapter, DbAdapter $dbAdapter);
}
