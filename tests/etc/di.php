<?php

use Magento\Framework\DB;
use Magento\Framework\Serialize;
use Magento\Framework\App;
use Magento\Framework\Model\ResourceModel;
use EcomDev\DatabaseWrapper\TestUtilities;

return [
    'preferences' => [
        DB\LoggerInterface::class => DB\Logger\Quiet::class,
        Serialize\SerializerInterface::class => Serialize\Serializer\Json::class,
        App\ResourceConnection\ConnectionAdapterInterface::class => ResourceModel\Type\Db\Pdo\Mysql::class,
        App\DeploymentConfig\Reader::class => TestUtilities\TestDeploymentConfigReader::class,
        ResourceModel\Type\Db\ConnectionFactoryInterface::class => ResourceModel\Type\Db\ConnectionFactory::class,
        \Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory::class
    ],
    DB\Select\SelectRenderer::class => [
        'arguments' => [
            'renderers' => [
                'distinct' => [
                    'renderer' => ['instance' => DB\Select\DistinctRenderer::class],
                    'sort' => 100,
                    'part' => 'distinct',
                ],
                'columns' => [
                    'renderer' => ['instance' => DB\Select\ColumnsRenderer::class],
                    'sort' => 200,
                    'part' => 'columns',
                ],
                'union' => [
                    'renderer' => ['instance' => DB\Select\UnionRenderer::class],
                    'sort' => 300,
                    'part' => 'union',
                ],
                'from' => [
                    'renderer' => ['instance' => DB\Select\FromRenderer::class],
                    'sort' => 400,
                    'part' => 'from',
                ],
                'where' => [
                    'renderer' => ['instance' => DB\Select\WhereRenderer::class],
                    'sort' => 500,
                    'part' => 'where',
                ],
                'group' => [
                    'renderer' => ['instance' => DB\Select\GroupRenderer::class],
                    'sort' => 600,
                    'part' => 'group',
                ],
                'having' => [
                    'renderer' => ['instance' => DB\Select\HavingRenderer::class],
                    'sort' => 700,
                    'part' => 'having',
                ],
                'order' => [
                    'renderer' => ['instance' => DB\Select\OrderRenderer::class],
                    'sort' => 800,
                    'part' => 'order',
                ],
                'limitcount' => [
                    'renderer' => ['instance' => DB\Select\LimitRenderer::class],
                    'sort' => 900,
                    'part' => 'limitcount',
                ],
                'forupdate' => [
                    'renderer' => ['instance' => DB\Select\ForUpdateRenderer::class],
                    'sort' => 1000,
                    'part' => 'forupdate',
                ],
            ],
        ],
    ],
];
