<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\DatabaseWrapper\TestUtilities;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Factory\Dynamic\Developer as RuntimeDynamicFactory;

class ConfiguredObjectManagerFactory
{
    public static function createObjectManager(TestConfiguration $testConfiguration)
    {
        $configuration = new Config();
        $dynamicFactory = new RuntimeDynamicFactory($configuration);

        $sharedInstances = [TestConfiguration::class => $testConfiguration];

        $objectManager = new ObjectManager($dynamicFactory, $configuration, $sharedInstances);
        $dynamicFactory->setObjectManager($objectManager);

        $objectManager->configure($testConfiguration->getDependencyInjection());

        return $objectManager;
    }
}
