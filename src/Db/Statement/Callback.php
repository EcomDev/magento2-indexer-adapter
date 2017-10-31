<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper\Db\Statement;


use EcomDev\IndexerWrapper\Db\DbAdapter;
use EcomDev\IndexerWrapper\Db\Statement;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Callback implements Statement
{
    /**
     * Callback for query building
     *
     * @var callable
     */
    private $callback;

    /**
     * Configures callback queries
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function execute(AdapterInterface $magentoAdapter, DbAdapter $dbAdapter)
    {
        return call_user_func($this->callback, $magentoAdapter, $dbAdapter);
    }
}
