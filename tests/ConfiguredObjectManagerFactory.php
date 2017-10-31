<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

namespace EcomDev\IndexerWrapper;

use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Factory\Dynamic\Developer as RuntimeDynamicFactory;
use Magento\Framework\App\ObjectManager;

class ConfiguredObjectManagerFactory
{
    const DEFAULT_DI_FILE = __DIR__ . '/_fixture/di.php';

    private $diFile;


    public function __construct($diFile = self::DEFAULT_DI_FILE)
    {
        $this->diFile = $diFile;
    }

    public function createObjectManager()
    {
        $configuration = new Config();
        $dynamicFactory = new RuntimeDynamicFactory($configuration);

        $objectManager = new ObjectManager($dynamicFactory, $configuration);
        $dynamicFactory->setObjectManager($objectManager);

        $this->loadObjectManagerConfiguration($objectManager);

        return $objectManager;
    }

    private function loadObjectManagerConfiguration(ObjectManager $objectManager)
    {
        $objectManager->configure($this->loadDiFile());
    }

    private function loadDiFile()
    {
        $configuration = include($this->diFile);
        return $configuration ?: [];
    }
}
