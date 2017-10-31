<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;

use Magento\Framework\DB\Adapter\AdapterInterface;

class TableList extends Callback
{
    public function __construct()
    {
        parent::__construct(function (AdapterInterface $magentoAdapter) {
            return $magentoAdapter->fetchCol('SHOW TABLES;');
        });
    }
}
