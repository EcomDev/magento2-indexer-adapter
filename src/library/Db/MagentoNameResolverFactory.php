<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\Db;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class MagentoNameResolverFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createResolver(ResourceConnection $resourceConnection): MagentoNameResolver
    {
        return $this->objectManager->create(MagentoNameResolver::class, [
            'resourceConnection' => $resourceConnection
        ]);
    }
}
