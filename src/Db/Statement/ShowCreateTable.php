<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;


use Magento\Framework\DB\Adapter\AdapterInterface;

class ShowCreateTable extends Callback
{
    public function __construct($tableName)
    {
        parent::__construct(function (AdapterInterface $magentoAdapter) use ($tableName) {
            return $magentoAdapter->fetchOne(sprintf('SHOW CREATE TABLE %s', $tableName));
        });
    }
}
