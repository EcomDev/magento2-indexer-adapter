<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use Magento\Framework\ObjectManagerInterface;

class QueryResultFactory
{
    /**
 * @var ObjectManagerInterface
*/
    private $objectManager;


    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createFromPdoStatement(\Zend_Db_Statement_Pdo $statement): QueryResult
    {
        return $this->objectManager->create(QueryResult::class, ['statement' => $statement]);
    }
}
